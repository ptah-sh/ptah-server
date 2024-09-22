import { usePage } from "@inertiajs/vue3";

export async function sha256(message) {
    if (typeof message !== "string") {
        message = JSON.stringify(message);
    }

    const msgBuffer = new TextEncoder().encode(message);

    const hashBuffer = await crypto.subtle.digest("SHA-256", msgBuffer);

    const hashArray = Array.from(new Uint8Array(hashBuffer));

    return hashArray.map((b) => b.toString(16).padStart(2, "0")).join("");
}

async function encrypt(value, publicKey) {
    publicKey = publicKey.split(/\n/g).slice(1, -1).join("");

    const binaryDerString = atob(publicKey);

    const binaryDer = new Uint8Array(
        binaryDerString.split("").map((char) => char.charCodeAt(0)),
    );

    const cryptoKey = await crypto.subtle.importKey(
        "spki",
        binaryDer,
        {
            name: "RSA-OAEP",
            hash: "SHA-256",
        },
        true,
        ["encrypt"],
    );

    const encoder = new TextEncoder();
    const data = encoder.encode(value);

    const encryptedData = await crypto.subtle.encrypt(
        {
            name: "RSA-OAEP",
            label: encoder.encode(""),
        },
        cryptoKey,
        data,
    );

    return btoa(String.fromCharCode.apply(null, new Uint8Array(encryptedData)));
}

export function useCrypto() {
    const page = usePage();
    const publicKey = page.props.node.swarm.data.encryptionKey;

    return {
        encrypt: (value) => encrypt(value, publicKey),
        encryptDeploymentData: (deploymentData) =>
            encryptDeploymentData(deploymentData, publicKey),
    };
}

export async function encryptDeploymentData(deploymentData, publicKey) {
    const encryptedData = {
        ...deploymentData,
        processes: [],
    };

    for (const process of deploymentData.processes) {
        const encryptedProcess = {
            ...process,
            secretFiles: [],
            secretVars: [],
        };

        for (const secretFile of process.secretFiles) {
            const encryptedSecretFile = {
                ...secretFile,
                content: secretFile.content
                    ? await encrypt(secretFile.content, publicKey)
                    : null,
            };

            encryptedProcess.secretFiles.push(encryptedSecretFile);
        }

        for (const secretVar of process.secretVars) {
            const encryptedSecretVar = {
                ...secretVar,
                value: secretVar.value
                    ? await encrypt(secretVar.value, publicKey)
                    : null,
            };

            encryptedProcess.secretVars.push(encryptedSecretVar);
        }

        encryptedData.processes.push(encryptedProcess);
    }

    return encryptedData;
}

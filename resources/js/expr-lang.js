import Handlebars from "handlebars";

Handlebars.registerHelper("base64Encode", (context) => {
    return btoa(context);
});

Handlebars.registerHelper("randomBytes", (context) => {
    const buffer = new Uint8Array(context.length);

    self.crypto.getRandomValues(buffer);

    return String.fromCharCode.apply(null, buffer);
});

Handlebars.registerHelper("randomUsername", (context) => {
    const adjectives = [
        "happy",
        "sad",
        "angry",
        "excited",
        "bored",
        "sleepy",
        "hungry",
        "thirsty",
        "tired",
        "sick",
    ];
    const nouns = [
        "cat",
        "dog",
        "bird",
        "fish",
        "monkey",
        "elephant",
        "tiger",
        "lion",
        "zebra",
        "giraffe",
    ];

    const adjective = adjectives[Math.floor(Math.random() * adjectives.length)];
    const noun = nouns[Math.floor(Math.random() * nouns.length)];

    return `${adjective}_${noun}`;
});

Handlebars.registerHelper("randomPassword", (context) => {
    // TODO: add an option to use special characters - !@#$%^&*()_+~`|[]:;?><,./-=
    //   current alphabet is safe for most use cases (i.e. as "database URL" in form postgresql://user:pass@host:port/db)

    const characters =
        "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

    const password = Array.from(
        { length: context.length || 42 },
        () => characters[Math.floor(Math.random() * characters.length)],
    ).join("");

    return password;
});

Handlebars.registerHelper("randomString", (context) => {
    const characters =
        "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()_+~`|[]:;?><,./-=";

    const string = Array.from(
        { length: context.length || 64 },
        () => characters[Math.floor(Math.random() * characters.length)],
    ).join("");

    return string;
});

export function evaluate(template, data) {
    const compiled = Handlebars.compile(template, {
        strict: true,
        noEscape: true,
    });

    return compiled(data);
}

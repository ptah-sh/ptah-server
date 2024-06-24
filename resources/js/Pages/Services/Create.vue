<script setup>

import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import TextInput from "@/Components/TextInput.vue";
import ActionSection from "@/Components/ActionSection.vue";
import {useForm} from "@inertiajs/vue3";
import FormField from "@/Components/FormField.vue";
import Select from "@/Components/Select.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import TextArea from "@/Components/TextArea.vue";
import {computed, effect, reactive} from "vue";

// TODO: !!!!!!! When implementing EDIT functionality
//   need to add tons of readonly inputs to preserve docker id and docker names for resources
//   It should be created in a separate component which will display these values side-by-side.

const props = defineProps({
  'swarms': Array,
  'networks': Array,
  'nodes': Array,
})

const form = useForm({
  name: '',
  swarm_id: props.swarms[0]?.id,
  dockerRegistryId: '',
  dockerImage: '',
  envVars: [],
  secretVars: [],
  configFiles: [],
  secretFiles: [],
  placementNodeId: '',
  volumes: [],
  networkName: props.networks[0]?.docker_name,
  internalDomain: '',
  ports: [],
  replicas: "1",
  caddy: [],
  fastCgi: null
});

const state = reactive({
  internalDomainTouched: false,
});

const makeId = (prefix) =>  prefix + '-' + Math.random().toString(36).slice(2);

const addEnvVar = () => {
  form.envVars.push({id: makeId('env'), name: '', value: ''});
}

const addSecretVar = () => {
  form.secretVars.push({id: makeId('secret-var'), name: '', value: ''});
}

const addConfigFile = () => {
  form.configFiles.push({id: makeId('config'), name: '', content: ''});
}

const addSecretFile = () => {
  form.secretFiles.push({id: makeId('secret-file'), name: '', content: ''});
}

const addVolume = () => {
  form.volumes.push({id: makeId('volume'), name: '', path: ''});
}

const addPort = () => {
  form.ports.push({id: makeId('port'), targetPort: '', publishedPort: ''});
}

const addCaddy = () => {
  form.caddy.push({
    id: makeId('caddy'),
    targetProtocol: 'http',
    targetPort: '',
    publishedPort: "443",
    domain: '',
    path: '',
  });
}

const addFastCgiVar = () => {
  form.fastCgi.env.push({id: makeId('fastcgi') , name: '', value: ''});
}

const hasFastCgiHandlers = computed(() => {
  return form.caddy.some((caddy) => caddy.targetProtocol === 'fastcgi')
})

effect(() => {
    form.fastCgi = hasFastCgiHandlers.value
        ? {
          root: '',
          env: [],
        }
        : null;

    if (!state.internalDomainTouched) {
      form.internalDomain = form.name + '.svc.local';
    }
});

const createService = () => {
  form.post(route('services.store'))
}
</script>

<template>
  <AppLayout title="Dashboard">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        Create Service
      </h2>
    </template>

    <div class="py-12">
      <form class="max-w-7xl mx-auto sm:px-6 lg:px-8" @submit.prevent="createService">
        <ActionSection>
          <template #title>
            Service Details
          </template>

          <template #description>
            <p>Select a swarm where the service will be deployed to.</p>
            <p>The name of the service can be changed at any time. Be aware, that the service name in the Swarm cluster
              will not be changed.</p>
          </template>

          <template #content>
            <div class="col-span-6">
              <div class="flex items-center mt-2">
                <img class="object-cover w-12 h-12 rounded-full"
                     :src="'https://ui-avatars.com/api/?name=' + encodeURIComponent($page.props.auth.user.current_team.name) + '&color=7F9CF5&background=EBF4FF'"
                     :alt="$page.props.auth.user.current_team.name">

                <div class="ms-4 leading-tight">
                  <div class="text-gray-900 dark:text-white">{{ $page.props.auth.user.current_team.name }}</div>
                </div>
              </div>
            </div>

            <FormField :error="form.errors.swarm_id">
              <template #label>Swarm</template>

              <Select v-model="form.swarm_id">
                <option v-for="swarm in $page.props.swarms" :value="swarm.id">{{ swarm.name }}</option>
              </Select>
            </FormField>

            <FormField :error="form.errors.name">
              <template #label>Service Name</template>

              <TextInput v-model="form.name" class="block w-full"/>
            </FormField>
          </template>
        </ActionSection>

        <ActionSection>
          <template #title>
            Docker Image
          </template>

          <template #description>
            Select a base Docker image to be used by the service.
          </template>

          <template #content>
            <FormField :error="form.errors.dockerRegistryId">
              <template #label>Docker Registry</template>

              <Select v-model="form.dockerRegistryId">
                <option value="">Docker Hub (or another one with a public access)</option>
                <!--                <option v-for="registry in $page.props.docker_registries" :value="registry.id">{{ registry.name }}</option>-->
              </Select>
            </FormField>

            <FormField :error="form.errors.dockerImage">
              <template #label>Docker Image</template>

              <TextInput v-model="form.dockerImage" class="block w-full" placeholder="nginxdemos/hello:latest"/>
            </FormField>
          </template>
        </ActionSection>

        <ActionSection>
          <template #title>
            Environment Variables
          </template>

          <template #description>
            Add environment variables to the service. These variables will be stored on the Ptah.sh database and will be
            fully accessible to edit them via UI.
          </template>

          <template #content>
            <InputError :message="form.errors.envVars" class="mt-2"/>

            <div v-if="form.envVars.length === 0" class="col-span-6">
              No Environment Variables defined
            </div>
            <FormField v-else v-for="(envVar, index) in form.envVars" :key="envVar.id"
                       :error="form.errors[`envVars.${index}.name`] || form.errors[`envVars.${index}.value`]">
              <template #label v-if="index === 0">Environment Variables</template>

              <div class="flex gap-2">
                <TextInput v-model="envVar.name" class="w-56" placeholder="Name"/>
                <TextInput v-model="envVar.value" class="grow" placeholder="Value"/>

                <SecondaryButton @click="form.envVars.splice(index, 1)" tabindex="-1">
                  <svg class="w-4 h-4 text-gray-800 dark:text-white" aria-hidden="true"
                       xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M5 12h14"/>
                  </svg>
                </SecondaryButton>
              </div>
            </FormField>
          </template>

          <template #actions>
            <SecondaryButton @click="addEnvVar">
              <svg class="w-4 h-4 me-2 -ms-1 text-gray-800 dark:text-white" aria-hidden="true"
                   xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M5 12h14m-7 7V5"/>
              </svg>
              Environment Variable
            </SecondaryButton>
          </template>
        </ActionSection>

        <ActionSection>
          <template #title>
            Secret Variables
          </template>

          <template #description>
            <p>Add secret variables to the service. Only variable names will be stored on the Ptah.sh database. You
              wouldn't be able to view their contents, but you'd able to provide new values.</p>
            <p>Secret Variables are stored as Docker Configs and you will be able to see it's contents via Docker CLI.</p>
          </template>

          <template #content>
            <InputError :message="form.errors.secretVars" class="mt-2"/>

            <div v-if="form.secretVars.length === 0" class="col-span-6">
              No Secret Variables defined
            </div>
            <FormField v-else v-for="(secretVar, index) in form.secretVars" :key="secretVar.id"
                       :error="form.errors[`secretVars.${index}.name`] || form.errors[`secretVars.${index}.value`]">
              <template #label v-if="index === 0">Secret Variables</template>

              <div class="flex gap-2">
                <TextInput v-model="secretVar.name" class="w-56" placeholder="Name"/>
                <TextInput v-model="secretVar.value" class="grow" placeholder="Value"/>

                <SecondaryButton @click="form.secretVars.splice(index, 1)" tabindex="-1">
                  <svg class="w-4 h-4 text-gray-800 dark:text-white" aria-hidden="true"
                       xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M5 12h14"/>
                  </svg>
                </SecondaryButton>
              </div>
            </FormField>
          </template>

          <template #actions>
            <SecondaryButton @click="addSecretVar">
              <svg class="w-4 h-4 me-2 -ms-1 text-gray-800 dark:text-white" aria-hidden="true"
                   xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M5 12h14m-7 7V5"/>
              </svg>
              Secret Variable
            </SecondaryButton>
          </template>
        </ActionSection>

        <ActionSection>
          <template #title>
            Config Files
          </template>

          <template #description>
            <p>Add config files to mount into the container. Config files will be stored on the Ptah.sh database and
              will be fully accessible to edit them via UI.</p>
            <p>Config Files are stored as Docker Config Files and will be mounted directly into the container.</p>
          </template>

          <template #content>
            <div v-if="form.configFiles.length === 0" class="col-span-6">
              No Config Files defined
            </div>
            <template v-else v-for="(configFile, index) in form.configFiles" :key="configFile.id">
              <FormField :error="form.errors[`configFiles.${index}.path`]">
                <template #label>Path</template>

                <div class="flex gap-2">
                  <TextInput v-model="configFile.path" class="block grow"/>

                  <SecondaryButton @click="form.configFiles.splice(index, 1)" tabindex="-1">
                    <svg class="w-4 h-4 text-gray-800 dark:text-white" aria-hidden="true"
                         xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                      <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5 12h14"/>
                    </svg>
                  </SecondaryButton>
                </div>
              </FormField>
              <FormField :error="form.errors[`configFiles.${index}.content`]">
                <template #label>Config File Content</template>

                <TextArea v-model="configFile.content" class="block w-full" rows="3"/>
              </FormField>
            </template>
          </template>

          <template #actions>
            <SecondaryButton @click="addConfigFile">
              <svg class="w-4 h-4 me-2 -ms-1 text-gray-800 dark:text-white" aria-hidden="true"
                   xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M5 12h14m-7 7V5"/>
              </svg>
              Config File
            </SecondaryButton>
          </template>
        </ActionSection>

        <ActionSection>
          <template #title>
            Secret Files
          </template>

          <template #description>
            <p>Add secret files to mount into the container. Secret files will be stored as Docker Secrets.</p>
            <p>You will not be able to view their contents unless you mount them into a one-time container via Docker CLI.</p>
            <p>You can provide a new value to overwrite an existing one at any time via the UI.</p>
            <p>You can safely store .env or private encryption keys here.</p>
          </template>

          <template #content>
            <div v-if="form.secretFiles.length === 0" class="col-span-6">
              No Secret Files defined
            </div>
            <template v-else v-for="(secretFile, index) in form.secretFiles" :key="secretFile.id">
              <FormField :error="form.errors[`secretFiles.${index}.path`]">
                <template #label>Path</template>

                <div class="flex gap-2">
                  <TextInput v-model="secretFile.path" class="block grow"/>

                  <SecondaryButton @click="form.secretFiles.splice(index, 1)" tabindex="-1">
                    <svg class="w-4 h-4 text-gray-800 dark:text-white" aria-hidden="true"
                         xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                      <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5 12h14"/>
                    </svg>
                  </SecondaryButton>
                </div>
              </FormField>
              <FormField :error="form.errors[`secretFiles.${index}.content`]">
                <template #label>Secret File Content</template>

                <TextArea v-model="secretFile.content" class="block w-full" rows="3"/>
              </FormField>
            </template>
          </template>

          <template #actions>
            <SecondaryButton @click="addSecretFile">
              <svg class="w-4 h-4 me-2 -ms-1 text-gray-800 dark:text-white" aria-hidden="true"
                   xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M5 12h14m-7 7V5"/>
              </svg>
              Secret File
            </SecondaryButton>
          </template>
        </ActionSection>

        <ActionSection>
          <template #title>
            Persistent Volumes
          </template>

          <template #description>
            <p>Use Persistent Volumes if you to store your data between container restarts. Databases are the most
              common users of Persistent Volumes.</p>
            <p>Please note, you'll have to pick a Node to host your Persistent Volumes (and so launch containers
              on).</p>
          </template>

          <template #content>
            <InputError :message="form.errors.volumes" class="mt-2"/>

            <div v-if="form.volumes.length === 0" class="col-span-6">
              No Persistent Volumes defined
            </div>
            <FormField v-else v-for="(volume, index) in form.volumes" :key="volume.id"
                       :error="form.errors[`volumes.${index}.name`] || form.errors[`volumes.${index}.path`]">
              <template #label v-if="index === 0">Persistent Volumes</template>

              <div class="flex gap-2">
                <TextInput v-model="volume.name" class="w-56" placeholder="Name"/>
                <TextInput v-model="volume.path" class="grow" placeholder="Path"/>

                <SecondaryButton @click="form.volumes.splice(index, 1)" tabindex="-1">
                  <svg class="w-4 h-4 text-gray-800 dark:text-white" aria-hidden="true"
                       xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M5 12h14"/>
                  </svg>
                </SecondaryButton>
              </div>
            </FormField>
          </template>

          <template #actions>
            <SecondaryButton @click="addVolume">
              <svg class="w-4 h-4 me-2 -ms-1 text-gray-800 dark:text-white" aria-hidden="true"
                   xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M5 12h14m-7 7V5"/>
              </svg>
              Persistent Volume
            </SecondaryButton>
          </template>
        </ActionSection>

        <ActionSection>
          <template #title>
            Network
          </template>

          <template #description>
            <p>You can isolate containers from each other by using different networks.</p>
            <p>Service will be accessible by the internal domain name to other containers on the same network.</p>
            <p>You can make certain ports of the containers accessible from the host network. Please set up the firewall
              rule on your host to prevent unwanted access.</p>
          </template>

          <template #content>
            <FormField :error="form.errors.networkName">
              <template #label>Attach to Network</template>

              <Select v-model="form.networkName">
                <option v-for="network in $page.props.networks" :value="network.docker_name">{{ network.name }}</option>
              </Select>
            </FormField>

            <FormField :error="form.errors.internalDomain">
              <template #label>Internal Domain Name</template>

              <TextInput v-model="form.internalDomain" @change="state.internalDomainTouched = true" class="w-full"/>
            </FormField>

            <InputError :message="form.errors.ports" class="mt-2"/>

            <template v-for="(port, index) in form.ports" :key="port.id">
              <FormField
                  :error="form.errors[`ports.${index}.targetPort`] || form.errors[`ports.${index}.publishedPort`]">
                <template #label v-if="index === 0">Ports</template>

                <div class="flex gap-2">
                  <TextInput v-model="port.targetPort" class="w-56" placeholder="8080"/>
                  <TextInput v-model="port.publishedPort" class="grow" placeholder="80"/>
                  <SecondaryButton @click="form.ports.splice(index, 1)" tabindex="-1">
                    <svg class="w-4 h-4 text-gray-800 dark:text-white" aria-hidden="true"
                         xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                      <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5 12h14"/>
                    </svg>
                  </SecondaryButton>
                </div>
              </FormField>
            </template>
          </template>

          <template #actions>
            <SecondaryButton @click="addPort">
              <svg class="w-4 h-4 me-2 -ms-1 text-gray-800 dark:text-white" aria-hidden="true"
                   xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M5 12h14m-7 7V5"/>
              </svg>
              Port
            </SecondaryButton>
          </template>
        </ActionSection>

        <ActionSection>
          <template #title>
            Deployment
          </template>

          <template #description>
            <p>Pick the number of container instances you want to run for the service and the node you want to run them
              on.</p>
            <p>If you don't select the Placement Node, the containers will be able to run on any node of the Swarm
              Cluster.</p>
          </template>

          <template #content>
            <FormField :error="form.errors.replicas">
              <template #label>Replicas</template>
              <TextInput v-model="form.replicas" class="block w-full"/>
            </FormField>

            <FormField :error="form.errors.placementNodeId">
              <template #label>Placement Node</template>

              <Select v-model="form.placementNodeId">
                <option value="">Run on all Nodes</option>
                <option v-for="node in $page.props.nodes" :value="node.id">{{ node.name }}</option>
              </Select>
            </FormField>
          </template>
        </ActionSection>

        <ActionSection>
          <template #title>
            Public Access over the Internet
          </template>

          <template #description>
            <p>You can make certain ports of the containers accessible from the internet. All requests will be forwarded
              to your containers via Caddy.</p>
            <p>Currently you can expose only HTTP services via Caddy reverse-proxy.</p>
          </template>

          <template #content>
            <InputError :message="form.errors.caddy"/>

            <div v-if="form.caddy.length === 0">
              The service will be not exposed to the internet.
            </div>

            <div v-for="(caddy, index) in form.caddy" :key="caddy.id">
              <hr v-if="index > 0" class="my-4"/>

              <FormField :error="form.errors[`caddy.${index}.targetProtocol`] || form.errors[`caddy.${index}.targetPort`] || form.errors[`caddy.${index}.publishedPort`] || form.errors[`caddy.${index}.domain`] || form.errors[`caddy.${index}.path`]">
                <div class="flex gap-2">
                  <div class="w-36">
                    <InputLabel>Container Protocol</InputLabel>
                    <Select v-model="caddy.targetProtocol">
                      <option value="http">HTTP</option>
                      <option value="fastcgi">FastCGI</option>
                    </Select>
                  </div>

                  <div class="w-28">
                    <InputLabel>Container Port</InputLabel>
                    <TextInput v-model="caddy.targetPort" placeholder="8080" class="w-full"/>
                  </div>
                  <div class="w-28">
                    <InputLabel>Published Port</InputLabel>
<!--                    <TextInput v-model="caddy.publishedPort" placeholder="443" class="w-full"/>-->
                    <Select v-model="caddy.publishedPort">
                      <option value="443">HTTPS</option>
                      <option value="80">HTTP</option>
                    </Select>
                  </div>

                  <div class="grow">
                    <InputLabel>Domain</InputLabel>
                    <TextInput v-model="caddy.domain" class="w-full" placeholder="example.com"/>
                  </div>
                  <div class="grow">
                    <InputLabel>Path</InputLabel>
                    <TextInput v-model="caddy.path" class="w-full" placeholder="/*"/>
                  </div>
                  <div class="flex flex-col">
                    <InputLabel value="&nbsp;"/>
                    <SecondaryButton @click="form.caddy.splice(index, 1)" tabindex="-1" class="grow">
                      <svg class="w-4 h-4 text-gray-800 dark:text-white" aria-hidden="true"
                           xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M5 12h14"/>
                      </svg>
                    </SecondaryButton>
                  </div>
                </div>
              </FormField>
            </div>

            <template v-if="hasFastCgiHandlers">
              <FormField :error="form.errors['fastCgi.root']">
                <template #label>FastCGI Root</template>

                <TextInput v-model="form.fastCgi.root" class="grow" placeholder="/app/public"/>
              </FormField>

              <hr v-if="form.fastCgi.env.length > 0" class="my-4"/>

              <InputError :message="form.errors['fastCgi.env']"/>

              <template v-for="(fastcgiVar, index) in form.fastCgi.env" :key="fastcgiVar.id">
                <FormField :error="form.errors[`fastCgi.env.${index}.name`] || form.errors[`fastCgi.env.${index}.value`]">
                  <template #label v-if="index === 0">FastCGI Variables</template>

                  <div class="flex gap-2">
                    <TextInput v-model="fastcgiVar.name" class="w-56" placeholder="SCRIPT_FILENAME"/>
                    <TextInput v-model="fastcgiVar.value" class="grow" placeholder="/app/public/index.php"/>

                    <SecondaryButton @click="form.fastCgi.env.splice(index, 1)" tabindex="-1">
                      <svg class="w-4 h-4 text-gray-800 dark:text-white" aria-hidden="true"
                           xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                           viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2"
                              d="M5 12h14"/>
                      </svg>
                    </SecondaryButton>
                  </div>
                </FormField>
              </template>
            </template>
          </template>

          <template #actions>
            <SecondaryButton @click="addCaddy">
              <svg class="w-4 h-4 me-2 -ms-1 text-gray-800 dark:text-white" aria-hidden="true"
                   xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M5 12h14m-7 7V5"/>
              </svg>
              Public Endpoint
            </SecondaryButton>
            <SecondaryButton v-if="hasFastCgiHandlers" @click="addFastCgiVar">
              <svg class="w-4 h-4 me-2 -ms-1 text-gray-800 dark:text-white" aria-hidden="true"
                   xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M5 12h14m-7 7V5"/>
              </svg>

              FastCGI Variable
            </SecondaryButton>
          </template>
        </ActionSection>

        <div class="flex justify-end">
          <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
            Create Service
          </PrimaryButton>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
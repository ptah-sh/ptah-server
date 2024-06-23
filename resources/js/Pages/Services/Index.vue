<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import NodeStatus from "@/Components/NodeStatus.vue";
import {router} from "@inertiajs/vue3";
import PrimaryButton from "@/Components/PrimaryButton.vue";
</script>

<template>
  <AppLayout title="Services">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight flex" >
        Services
      </h2>
    </template>

    <template #actions>
      <PrimaryButton type="button" @click="router.get(route('services.create'))">Create</PrimaryButton>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
this is your "Services" page.

      </div>
    </div>
  </AppLayout>
</template>
<script setup>

import {defineComponent, defineProps, computed} from "vue";
import { Link , router } from '@inertiajs/vue3';

window.r = router;

const props = defineProps({
    // active: Boolean,
    href: String,
})

const classes = computed(() => props.href.endsWith(router.page.url)
    ? 'inline-flex items-center justify-center p-4 text-blue-600 border-b-2 border-blue-600 rounded-t-lg active dark:text-blue-500 dark:border-blue-500 group'
    : 'inline-flex items-center justify-center p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300 group'
)

</script>

<template>
    <li class="me-2">
        <Link :href="$props.href" :class="classes">
            <div class="me-2">
                <slot name="icon" />
            </div>

            <slot name="title" />
        </Link>
    </li>
</template>

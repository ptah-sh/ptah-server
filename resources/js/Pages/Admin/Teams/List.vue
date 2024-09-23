<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import DataTable from "@/Components/DataTable.vue";
import { router } from "@inertiajs/vue3";

const props = defineProps({
    teams: Object,
});

const columns = [
    { header: "Name", key: "name" },
    { header: "Owner", key: "owner" },
    { header: "Nodes", key: "nodes_count" },
    { header: "Services", key: "services_count" },
    { header: "Deployments", key: "deployments_count" },
];

const navigateToTeamShow = (team) => {
    router.visit(route("admin.teams.show", team.id));
};
</script>

<template>
    <AdminLayout title="Teams">
        <div class="py-6">
            <div class="max-w-full mx-auto sm:px-4 lg:px-6">
                <DataTable
                    :data="teams"
                    :columns="columns"
                    :onRowClick="navigateToTeamShow"
                >
                    <template #owner="{ item }">
                        {{ item.owner.name }}
                    </template>

                    <template #nodes_count="{ item }">
                        {{ item.online_nodes_count }} / {{ item.nodes_count }}
                    </template>
                </DataTable>
            </div>
        </div>
    </AdminLayout>
</template>

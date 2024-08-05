<script setup>
import SectionBorder from "@/Components/SectionBorder.vue";
import TeamMemberManager from "@/Pages/Teams/Partials/TeamMemberManager.vue";
import UpdateTeamNameForm from "@/Pages/Teams/Partials/UpdateTeamNameForm.vue";
import TeamsLayout from "@/Pages/Teams/TeamsLayout.vue";
import DeleteResourceSection from "@/Components/DeleteResourceSection.vue";
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
    team: Object,
    availableRoles: Array,
    permissions: Object,
});

const form = useForm({});

const destroyTeam = () => {
    form.delete(route("teams.destroy", props.team), {
        errorBag: "deleteTeam",
    });
};
</script>

<template>
    <TeamsLayout :team="props.team">
        <UpdateTeamNameForm :team="team" :permissions="permissions" />

        <TeamMemberManager
            class="mt-10 sm:mt-0"
            :team="team"
            :available-roles="availableRoles"
            :user-permissions="permissions"
        />

        <template v-if="permissions.canDeleteTeam && !team.personal_team">
            <SectionBorder />

            <DeleteResourceSection
                resource-kind="Team"
                :resource-name="team.name"
                :destroy="destroyTeam"
            >
                <template #default>
                    <b>Your subscription will be cancelled immediately.</b>
                </template>
            </DeleteResourceSection>
        </template>
    </TeamsLayout>
</template>

<script setup>
import {
    ref,
    onMounted,
    onBeforeUnmount,
    computed,
    watch,
    nextTick,
} from "vue";
import { createPopper } from "nanopop";
import jump from "jump.js";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";

const props = defineProps({
    autoStart: {
        type: Boolean,
        default: false,
    },
    steps: {
        type: Array,
        required: true,
        default: () => [],
    },
    previousButtonText: {
        type: String,
        default: "Previous",
    },
    nextButtonText: {
        type: String,
        default: "Next",
    },
    finishButtonText: {
        type: String,
        default: "Finish",
    },
    onDontShowAgain: {
        type: Function,
        default: null,
    },
    dontShowText: {
        type: String,
        default: "Don't show this tour again",
    },
    tourId: {
        type: String,
        required: true,
    },
    skipButtonText: {
        type: String,
        default: "Skip",
    },
});

const isVisible = ref(false);
const currentStepIndex = ref(0);
const popupEl = ref(null);
const highlightEl = ref(null);

const currentStep = computed(() => props.steps[currentStepIndex.value]);
const isFirstStep = computed(() => currentStepIndex.value === 0);
const isLastStep = computed(
    () => currentStepIndex.value === props.steps.length - 1,
);

let popperInstance = null;

function positionElements(shouldJump = false) {
    if (!isVisible.value) {
        return;
    }

    // If no target is specified, center the popup and skip highlight
    if (!currentStep.value.target) {
        if (popupEl.value) {
            popupEl.value.className =
                "fixed z-50 bg-white rounded-lg shadow-lg p-6 max-w-sm centered-modal";
            popupEl.value.style.top = "50%";
            popupEl.value.style.left = "50%";
            popupEl.value.style.transform = "translate(-50%, -50%)";
        }

        return;
    }

    const targetEl = document.querySelector(currentStep.value.target);
    if (!targetEl || !popupEl.value || !highlightEl.value) {
        return;
    }

    // Show highlight element
    highlightEl.value.style.display = "block";

    // Position highlight
    const targetRadius = targetEl
        .computedStyleMap()
        .get("border-radius")
        .toString();
    const boundingRect = targetEl.getBoundingClientRect();
    const highlight = highlightEl.value;

    const rect = {
        top: boundingRect.top,
        left: boundingRect.left,
        width: boundingRect.width,
        height: boundingRect.height,
    };

    if (targetRadius === "0px") {
        rect.top -= 8;
        rect.left -= 8;
        rect.width += 16;
        rect.height += 16;
    }

    highlight.style.top = `${rect.top}px`;
    highlight.style.left = `${rect.left}px`;
    highlight.style.width = `${rect.width}px`;
    highlight.style.height = `${rect.height}px`;
    highlight.style.borderRadius =
        targetRadius === "0px" ? "8px" : targetRadius;
    highlight.style.borderWidth = "2px";

    if (popperInstance === null) {
        popperInstance = createPopper(targetEl, popupEl.value, {
            position: currentStep.value.position || "left",
            margin: 20,
        });
    }

    popupEl.value.className = `fixed z-50 bg-white rounded-lg shadow-lg p-6 max-w-sm popup-${currentStep.value.position || "left"}`;
    popperInstance.update();

    if (shouldJump) {
        jump(targetEl, {
            duration: 500,
            offset: -200,
        });
    }
}

function nextStep() {
    if (!isLastStep.value) {
        currentStepIndex.value++;
    }
}

function previousStep() {
    if (!isFirstStep.value) {
        currentStepIndex.value--;
    }
}

function startTour() {
    if (!shouldShowTour()) {
        return;
    }

    currentStepIndex.value = 0;
    isVisible.value = true;
}

function endTour() {
    isVisible.value = false;
    if (popperInstance) {
        popperInstance = null;
    }
}

// Reposition elements when step changes
watch([isVisible, currentStepIndex], () => {
    if (popperInstance) {
        popperInstance = null;
    }

    nextTick(() => {
        positionElements(true);
    });
});

// Handle window resize
function handleResize() {
    if (isVisible.value) {
        positionElements(false);
    }
}

// Add scroll handler
function handleScroll() {
    if (isVisible.value) {
        positionElements(false);
    }
}

function getTourKey() {
    return `ptah_sh_tour_${props.tourId}_hidden`;
}

function shouldShowTour() {
    return localStorage.getItem(getTourKey()) !== "true";
}

function setTourHidden(hidden) {
    localStorage.setItem(getTourKey(), hidden ? "true" : "false");
}

onMounted(() => {
    window.addEventListener("resize", handleResize);
    window.addEventListener("scroll", handleScroll); // Add scroll listener
    if (props.autoStart && shouldShowTour()) {
        startTour();
    }
});

onBeforeUnmount(() => {
    window.removeEventListener("resize", handleResize);
    window.removeEventListener("scroll", handleScroll); // Remove scroll listener
    if (popperInstance) {
        popperInstance = null;
    }
});

// Expose methods for external control
defineExpose({
    startTour,
    endTour,
});

function handleDontShowChange(checked) {
    setTourHidden(checked);
    if (props.onDontShowAgain) {
        props.onDontShowAgain(checked);
    }
}
</script>

<template>
    <Teleport to="body">
        <!-- Tour popup -->
        <div
            v-if="isVisible"
            ref="popupEl"
            class="fixed z-50 bg-white rounded-lg shadow-lg p-6 max-w-sm"
            :style="{ visibility: isVisible ? 'visible' : 'hidden' }"
        >
            <!-- Title if provided -->
            <h3 v-if="currentStep.title" class="text-lg font-semibold mb-2">
                {{ currentStep.title }}
            </h3>

            <!-- Content section -->
            <div class="prose">
                <!-- Regular content -->
                <template v-if="typeof currentStep.content === 'string'">
                    <p>{{ currentStep.content }}</p>
                </template>

                <!-- Custom content -->
                <template v-else-if="currentStep.content">
                    <component :is="currentStep.content" />
                </template>
            </div>

            <!-- Add checkbox before buttons -->
            <div class="flex items-center mt-4 mb-2">
                <input
                    type="checkbox"
                    id="dontShowAgain"
                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                    :checked="!shouldShowTour()"
                    @change="(e) => handleDontShowChange(e.target.checked)"
                />
                <label for="dontShowAgain" class="ml-2 text-sm text-gray-600">
                    {{ dontShowText }}
                </label>
            </div>

            <!-- Custom buttons if provided, otherwise default navigation -->
            <div class="flex justify-between mt-4">
                <template v-if="currentStep.buttons">
                    <component
                        :is="currentStep.buttons"
                        :next-step="nextStep"
                        :previous-step="previousStep"
                        :end-tour="endTour"
                        :is-first="isFirstStep"
                        :is-last="isLastStep"
                    />
                </template>
                <template v-else>
                    <div class="flex gap-2">
                        <SecondaryButton
                            v-if="!isFirstStep"
                            @click="previousStep"
                        >
                            {{ previousButtonText }}
                        </SecondaryButton>
                        <SecondaryButton @click="endTour" class="text-gray-500">
                            {{ skipButtonText }}
                        </SecondaryButton>
                    </div>
                    <div>
                        <PrimaryButton v-if="!isLastStep" @click="nextStep">
                            {{ nextButtonText }}
                        </PrimaryButton>
                        <PrimaryButton
                            v-if="isLastStep"
                            @click="endTour"
                            class="bg-green-600 hover:bg-green-700"
                        >
                            {{ finishButtonText }}
                        </PrimaryButton>
                    </div>
                </template>
            </div>
        </div>

        <!-- Keep the highlight element, but make it more visible -->
        <Transition name="fade">
            <div
                v-if="isVisible"
                ref="highlightEl"
                class="fixed z-40 transition-all duration-300 pointer-events-none border-indigo-600"
                :style="{
                    boxShadow:
                        '0 0 0 3px rgba(79, 70, 229, 0.5), 0 0 0 9999px rgba(0, 0, 0, 0.1)',
                }"
            />
        </Transition>
    </Teleport>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

/* Popup positioning and arrows */
.popup-left,
.popup-right,
.popup-top,
.popup-bottom {
    position: relative;
}

.popup-left::before,
.popup-right::before,
.popup-top::before,
.popup-bottom::before {
    content: "";
    position: absolute;
    width: 0;
    height: 0;
    border: 8px solid transparent;
}

/* Left position arrow */
.popup-left::before {
    right: -16px;
    top: 50%;
    transform: translateY(-50%);
    border-left-color: white;
    filter: drop-shadow(1px 0 1px rgba(0, 0, 0, 0.1));
}

/* Right position arrow */
.popup-right::before {
    left: -16px;
    top: 50%;
    transform: translateY(-50%);
    border-right-color: white;
    filter: drop-shadow(-1px 0 1px rgba(0, 0, 0, 0.1));
}

/* Top position arrow */
.popup-top::before {
    bottom: -16px;
    left: 50%;
    transform: translateX(-50%);
    border-top-color: white;
    filter: drop-shadow(0 1px 1px rgba(0, 0, 0, 0.1));
}

/* Bottom position arrow */
.popup-bottom::before {
    top: -16px;
    left: 50%;
    transform: translateX(-50%);
    border-bottom-color: white;
    filter: drop-shadow(0 -1px 1px rgba(0, 0, 0, 0.1));
}

/* Centered modal style */
.centered-modal::before {
    display: none; /* Hide arrow for centered modals */
}
</style>

<script setup>
import { defineProps, defineEmits, computed } from "vue";

const props = defineProps({
    modelValue: {
        type: String,
        default: "",
    },
    title: {
        type: String,
        default: "",
    },
    type: {
        type: String,
        default: "text",
        validator: (value) => {
            return ["text", "email", "password", "number", "tel"].includes(
                value
            );
        },
    },
    message: {
        type: String,
        default: "",
    },
    placeholder: {
        type: String,
        default: "",
    },
    icon: {
        type: Function,
        required: false,
    },
    required: {
        type: Boolean,
        default: false,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    maxLength: {
        type: Number,
        default: undefined,
    },
    minLength: {
        type: Number,
        default: undefined,
    },
    autocomplete: {
        type: String,
        default: "off",
    },
});

const emit = defineEmits(["update:modelValue", "focus", "blur", "input"]);

const inputClasses = computed(() => {
    return [
        "w-full",
        "h-12",
        "border",
        "border-gray-200",
        "rounded-xl",
        "focus:border-blue-500",
        "focus:ring-4",
        "focus:ring-blue-200/50",
        "transition",
        "duration-200",
        "ease-in-out",
        "placeholder-gray-400",
        "shadow-sm",
        props.icon ? "pl-12" : "pl-4",
        props.disabled ? "bg-gray-100 cursor-not-allowed" : "bg-white",
        props.message ? "border-red-500" : "border-gray-200",
    ];
});

// Обработчики событий
const handleInput = (event) => {
    emit("update:modelValue", event.target.value);
    emit("input", event);
};

const handleFocus = (event) => {
    emit("focus", event);
};

const handleBlur = (event) => {
    emit("blur", event);
};
</script>

<template>
    <div class="space-y-2 group">
        <label
            :for="title"
            class="text-sm font-semibold text-gray-600 ml-1 group-focus-within:text-blue-600 transition-colors flex items-center gap-1"
        >
            {{ title }}
            <span v-if="required" class="text-red-500">*</span>
        </label>

        <div class="relative">
            <span
                v-if="icon"
                class="absolute left-4 top-3.5 text-gray-400 pointer-events-none"
            >
                <component :is="icon" class="w-5 h-5" />
            </span>

            <input
                :id="title"
                :value="modelValue"
                @input="handleInput"
                @focus="handleFocus"
                @blur="handleBlur"
                :class="inputClasses"
                :type="type"
                :placeholder="placeholder"
                :disabled="disabled"
                :required="required"
                :maxlength="maxLength"
                :minlength="minLength"
                :autocomplete="autocomplete"
            />
        </div>

        <transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="transform -translate-y-1 opacity-0"
            enter-to-class="transform translate-y-0 opacity-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="transform translate-y-0 opacity-100"
            leave-to-class="transform -translate-y-1 opacity-0"
        >
            <p
                v-if="message"
                class="text-red-500 text-xs ml-1 flex items-center gap-1"
            >
                {{ message }}
            </p>
        </transition>
    </div>
</template>

<script setup lang="ts">
import { shallowRef, ref } from "vue";
import { defineForm, field, isValidForm, toObject } from "vue-yup-form";
import * as yup from "yup";
import { vMask } from "vue-the-mask";
import {useAuthStore} from '@/stores/authStore'
import { formToJSON } from "axios";

const generateForm = () => {
  const password = field("", yup.string().label("Password").min(8).required());
  const password_confirmation = field("", () =>
    yup
      .string()
      .label("Confirm Password")
      .required()
      .oneOf([password.$value], ({ label }) => `${label} does not match`)
  );

  return defineForm({
    name: field("", yup.string().label("First Name").required()),
    last_name: field("", yup.string().label("Last Name").required()),
    phone: field(
      "",
      yup
        .string()
        .label("Phone")
        .matches(/^\+?[\d\s-]{10,}$/, "Invalid phone number")
        .required()
    ),
    email: field(
      "",
      yup.string().label("Email").email().required()
    ),
    password,
    password_confirmation,
  });
};

const form = shallowRef(generateForm());
const submitted = ref(false);
const submitting = ref(false);

const onSubmit = async () => {
  submitted.value = true;
  submitting.value = true;

  try {
    if (!isValidForm(form.value)) {
      window.scrollTo(0, 0);
      return;
    }

    useAuthStore().register(toObject(form.value));
    console.log(toObject(form.value));
  } finally {
    submitting.value = false;
  }
};

const onReset = () => {
  form.value = generateForm();
};
</script>

<template>
  <div class="max-w-md mx-auto p-6 bg-white rounded-lg shadow-md">
    <h1 class="text-2xl font-bold text-center mb-6">Hesap Oluştur</h1>

    <form @submit.prevent="onSubmit" class="space-y-4">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1" for="name">
          {{ form.name.$label }}
        </label>
        <input
          id="name"
          name="name"
          type="text"
          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
          :class="{ 'border-red-500': submitted && form.name.$error }"
          v-model.trim="form.name.$value"
        />
        <span
          v-if="submitted && form.name.$error"
          class="text-sm text-red-600"
        >
          {{ form.name.$error.message }}
        </span>
      </div>

      <div>
        <label
          class="block text-sm font-medium text-gray-700 mb-1"
          for="last_name"
        >
          {{ form.last_name.$label }}
        </label>
        <input
          id="last_name"
          name="last_name"
          type="text"
          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
          :class="{ 'border-red-500': submitted && form.last_name.$error }"
          v-model.trim="form.last_name.$value"
        />
        <span
          v-if="submitted && form.last_name.$error"
          class="text-sm text-red-600"
        >
          {{ form.last_name.$error.message }}
        </span>
      </div>

      <div>
        <label
          class="block text-sm font-medium text-gray-700 mb-1"
          for="phone"
        >
          {{ form.phone.$label }}
        </label>
        <input
          id="phone"
          name="phone"
          type="tel"
          v-mask="'(###) ###-####'"
          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
          :class="{ 'border-red-500': submitted && form.phone.$error }"
          v-model.trim="form.phone.$value"
          placeholder="(555) 456-7890"
        />
        <span
          v-if="submitted && form.phone.$error"
          class="text-sm text-red-600"
        >
          {{ form.phone.$error.message }}
        </span>
      </div>

      <div>
        <label
          class="block text-sm font-medium text-gray-700 mb-1"
          for="email"
        >
          {{ form.email.$label }}
        </label>
        <input
          id="email"
          name="email"
          type="email"
          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
          :class="{ 'border-red-500': submitted && form.email.$error }"
          v-model.trim="form.email.$value"
          placeholder="example@domain.com"
        />
        <span
          v-if="submitted && form.email.$error"
          class="text-sm text-red-600"
        >
          {{ form.email.$error.message }}
        </span>
      </div>

      <div>
        <label
          class="block text-sm font-medium text-gray-700 mb-1"
          for="password"
        >
          {{ form.password.$label }}
        </label>
        <input
          id="password"
          name="password"
          type="password"
          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
          :class="{ 'border-red-500': submitted && form.password.$error }"
          v-model="form.password.$value"
        />
        <span
          v-if="submitted && form.password.$error"
          class="text-sm text-red-600"
        >
          {{ form.password.$error.message }}
        </span>
      </div>

      <div>
        <label
          class="block text-sm font-medium text-gray-700 mb-1"
          for="password_confirmation"
        >
          {{ form.password_confirmation.$label }}
        </label>
        <input
          id="password_confirmation"
          name="password_confirmation"
          type="password"
          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
          :class="{
            'border-red-500': submitted && form.password_confirmation.$error,
          }"
          v-model="form.password_confirmation.$value"
        />
        <span
          v-if="submitted && form.password_confirmation.$error"
          class="text-sm text-red-600"
        >
          {{ form.password_confirmation.$error.message }}
        </span>
      </div>

      <div class="flex items-center justify-between">
        <button
          type="submit"
          class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
          :disabled="submitting"
        >
          {{ submitting ? "İşleniyor..." : "Kayıt Ol" }}
        </button>
        <button
          type="button"
          @click="onReset"
          class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
          :disabled="submitting"
        >
          Sıfırla
        </button>
      </div>
    </form>

    <div class="mt-4 text-center">
      <p class="text-sm text-gray-600">
        Hesabınız var mı?
        <router-link
          to="/login"
          class="text-blue-600 hover:text-blue-800 font-medium"
          >Giriş Yap</router-link
        >
      </p>
    </div>
  </div>
</template>

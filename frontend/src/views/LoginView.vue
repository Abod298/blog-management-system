<script setup lang="ts">
import { ref } from "vue";
import {useAuthStore} from '@/stores/authStore'
import { defineForm, field, isValidForm, toObject } from "vue-yup-form";
import * as yup from "yup";
import { vMask } from "vue-the-mask";

const form = defineForm({
  login: field(
    "",
    yup
      .string()
      .label("Eposta Veya Telefon Numarası")
      .test(
        "is-email-or-phone",
        "Geçerli Eposta Veya Telefon Numarası Kullanın",
        (value) => {
          if (!value) return false;
          const isEmail = yup.string().email().isValidSync(value);
          const isPhone = /^\+?[\d\s-]{10,}$/.test(value);
          return isEmail || isPhone;
        }
      )
      .required()
  ),
  password: field("", yup.string().label("Password").required()),
});

const submitted = ref(false);
const submitting = ref(false);

const onSubmit = async () => {
  submitted.value = true;
  submitting.value = true;

  try {
    if (!isValidForm(form)) {
      window.scrollTo(0, 0);
      return;
    }
    useAuthStore().login({login:form.login.$value , password : form.password.$value})
  } finally {
    submitting.value = false;
  }
};
</script>

<template>
  <div class="max-w-md mx-auto p-6 bg-white rounded-lg shadow-md">
    <h1 class="text-2xl font-bold text-center mb-6">Giriş Yap</h1>

    <form @submit.prevent="onSubmit" class="space-y-4">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1" for="login">
          {{ form.login.$label }}
        </label>
        <input
          id="login"
          name="login"
          type="text"
          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
          :class="{ 'border-red-500': submitted && form.login.$error }"
          v-model.trim="form.login.$value"
          placeholder="email@example.com veya +1 (123) 456-7890"
          @input="(e) => {
            const value = e.target.value;
            if (value.startsWith('+') || /^\d/.test(value)) {
              e.target.type = 'tel';
            } else {
              e.target.type = 'text';
            }
          }"
          v-mask="form.login.$value.startsWith('+') || /^\d/.test(form.login.$value) ? '+1 (###) ###-####' : false"
        />
        <span
          v-if="submitted && form.login.$error"
          class="text-sm text-red-600"
        >
          {{ form.login.$error.message }}
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
        <button
          type="submit"
          class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
          :disabled="submitting"
        >
          {{ submitting ? "Giriş Yapılıyor..." : "Giriş Yap" }}
        </button>
      </div>
    </form>

    <div class="mt-4 text-center">
      <p class="text-sm text-gray-600">
        Hesabınız yok mu?
        <router-link
          to="/register"
          class="text-blue-600 hover:text-blue-800 font-medium"
          >Kayıt Ol</router-link
        >
      </p>
    </div>
  </div>
</template>

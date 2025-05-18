<template>
  <div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl vintage-header">Próximos Eventos</h1>

    <div class="flex items-center">
      <label for="cityFilter" class="mr-2 text-sm font-medium text-gray-700">
        Filtrar por ciudad:
      </label>
      <select
        id="cityFilter"
        v-model="selectedCity"
        class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 p-2 border"
      >
        <option value="">Todas las ciudades</option>
        <option v-for="city in cities" :key="city" :value="city">{{ city }}</option>
      </select>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    cities: Array,
    modelValue: String,
  },
  computed: {
    selectedCity: {
      get() {
        return this.modelValue;
      },
      set(value) {
        this.$emit("update:modelValue", value);
      },
    },
  },
};
</script>
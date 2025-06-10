<template>
  <div
    :class="[variantClasses.wrapper, fixedClasses.wrapper]"
    role="alert"
  >
    <!-- Contenido del mensaje de alerta -->
    <span :class="[variantClasses.body, fixedClasses.body]">
      <slot />
    </span>
    <!-- Botón para cerrar la alerta si es dismissible -->
    <button
      v-if="dismissible"
      type="button"
      :class="[variantClasses.close, fixedClasses.close]"
      @click="dismiss"
    >
      <svg
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 20 20"
        fill="currentColor"
        :class="fixedClasses.closeIcon"
      >
        <path
          fill-rule="evenodd"
          d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 011.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
          clip-rule="evenodd"
        />
      </svg>
    </button>
  </div>
</template>

<script>
export default {
  props: {
    variant: {
      type: String,
      default: 'default',
    },
    dismissible: {
      type: Boolean,
      default: true,
    },
  },
  data() {
    return {
      fixedClasses: {
        wrapper: 'relative flex items-center p-4 border-l-4 rounded shadow-sm',
        body: 'flex-grow',
        close: 'absolute relative flex items-center justify-center ml-4 flex-shrink-0 w-6 h-6 transition duration-100 ease-in-out rounded focus:ring-2 focus:ring-blue-500 focus:outline-none focus:ring-opacity-50',
        closeIcon: 'fill-current h-4 w-4',
      },
      classes: {
        default: {
          wrapper: 'bg-blue-50 border-blue-500',
          body: 'text-blue-700',
          close: 'text-blue-500 hover:bg-blue-200',
        },
        danger: {
          wrapper: 'bg-red-50 border-red-500',
          body: 'text-red-700',
          close: 'text-red-500 hover:bg-red-200',
        },
        success: {
          wrapper: 'bg-green-50 border-green-500',
          body: 'text-green-700',
          close: 'text-green-500 hover:bg-green-200',
        },
      },
    };
  },
  computed: {
    variantClasses() {
      return this.classes[this.variant] || this.classes.default;
    },
  },
  methods: {
    dismiss() {
      this.$emit('dismiss');
    },
  },
};
</script>

<style scoped>
/* Opcional: estilos adicionales */
</style>

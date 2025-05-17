<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-2xl font-bold text-gray-900">
                Bienvenido al Dashboard
            </h2>
        </template>

        <div class="py-12 bg-gray-100 min-h-screen">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="bg-white shadow-lg rounded-xl p-8 flex flex-col items-center justify-center space-y-6"
                >
                    <div class="text-center">
                        <h3 class="text-xl font-semibold text-gray-800">
                            ¡Sesión iniciada con éxito!
                        </h3>
                        <p class="text-gray-600 mt-2">
                            Gracias por iniciar sesión. Puedes ver la lista de
                            personas registradas a continuación.
                        </p>
                    </div>

                    <a
                        href="/personas/ver"
                        class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-medium px-6 py-3 rounded-lg shadow-md transition duration-200"
                    >
                        Lista
                    </a>

                    <a
                        href="/personas/crear"
                        class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-medium px-6 py-3 rounded-lg shadow-md transition duration-200"
                    >
                        Crear
                    </a>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

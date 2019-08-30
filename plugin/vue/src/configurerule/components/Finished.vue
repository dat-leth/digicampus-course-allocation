<template>
    <div id="finished">
        <h4>Fertig!</h4>
        <p>Die Anmelderegel wurde erfolgreich konfiguriert. Sie können den Dialog über 'Speichern' nun schließen.</p>
        <button class="button" v-on:click.prevent="prevStep" v-if="this.$store.state.step > 0">Zurück</button>
    </div>
</template>

<script>
    export default {
        name: "Finished",
        methods: {
            prevStep: function () {
                this.$store.dispatch("prevStep");
            }
        }
    }
</script>

<style scoped>

</style>
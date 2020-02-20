<template>
    <div id="app">
        <DistDateTime/>
        <CourseCapacity/>
        <RankingGroupList/>
    </div>
</template>

<script>
    import DistDateTime from "./components/DistDateTime";
    import CourseCapacity from "./components/CourseCapacity";
    import RankingGroupList from "./components/RankingGroupList";

    export default {
        name: 'configure_rule',
        components: {
            RankingGroupList,
            CourseCapacity,
            DistDateTime,
        },
        created() {
            // eslint-disable-next-line
            const instance = STUDIP.Dialog.getInstance('configurerule');
            if (instance !== undefined) {
                // eslint-disable-next-line
                jQuery(instance.element).dialog('option', 'width', window.screen.width / 3 * 2);
            }
            this.$store.dispatch("initialState");
        }
    }
</script>

<style>
</style>

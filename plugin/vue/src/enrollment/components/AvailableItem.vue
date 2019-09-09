<template>
    <div id="item">
        <button class="add-button" v-on:click.prevent="addToRanking(itemId)">
            <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"
                 shape-rendering="geometricPrecision" fill="#24437c">
                <path d="M8 1.001a7 7 0 1 0 .003 14 7 7 0 0 0-.003-14zm4.016 8.024H9.023l.002 2.943-2.048.001.001-2.943H3.984l-.001-2.05h2.994V4l2.047-.001v2.976l2.993.001-.001 2.049z"/>
            </svg>
        </button>
        <ul>
            <li v-for="(course) in courses" :key="course.seminar_id">
                <strong>{{ course.name }}</strong>
                <p v-html="course.formatted_date"></p>
            </li>
        </ul>
    </div>
</template>

<script>
    export default {
        name: "AvailableItem",
        props: ['courses', 'itemId'],
        methods: {
            addToRanking: function (itemId) {
                this.$store.dispatch("addToRanking", itemId);
            }
        }
    }
</script>

<style scoped>
    #item {
        display: flex;
        margin: 5px 0 5px 0;
        border: 1px solid #d0d7e3;
    }

    button {
        border: none;
        background-color: #e7ebf1;
    }

    ul {
        list-style-type: none;
        padding: 0;
        margin: 0;
        flex: 1 1 auto;
        display: flex;
        flex-direction: column;
        justify-content: space-around;
    }

    ul > li:not(:first-child):before {
        content: 'oder ';
    }

    ul > li {
        margin: 5px;
    }

    ul > li > p {
        margin: 0;
    }
</style>
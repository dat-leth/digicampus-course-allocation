<i18n>
{
	"de_DE": {
		"or": "oder"
	},
	"en_GB": {
		"or": "or"
	}
}
</i18n>

<template>
    <div id="ranking-item">
        <button class="add-button" v-on:click.prevent="removeFromRanking">
            <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"
                 shape-rendering="geometricPrecision" fill="#24437c">
                <path d="M8 1.001a7 7 0 1 0 .003 14 7 7 0 0 0-.003-14zm3.992 7.998L3.969 9l-.001-2 8.024.001v1.998z"/>
            </svg>
        </button>
        <ul>
            <li v-for="(course, i) in courses" :key="course.seminar_id">
                <span v-if="i > 0">{{ $t('or') }} </span><strong>{{ course.name }}</strong>
                <p v-html="course.formatted_date"></p>
            </li>
        </ul>

        <div class="prio">{{ index + 1 }}</div>
        <div class="handle"><img src="/assets/images/anfasser_24.png"/></div>
    </div>
</template>

<script>
    export default {
        name: "RankingItem",
        props: ['itemId', 'index'],
        computed: {
            courses() {
                return this.$store.getters.bundleItems[this.itemId]
            }
        },
        methods: {
            removeFromRanking() {
                this.$store.dispatch("removeFromRanking", this.itemId)
            }
        }
    }
</script>

<style scoped>
    #ranking-item {
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

    ul > li {
        margin: 5px;
    }

    ul > li > p {
        margin: 0;
    }


    .prio {
        display: flex;
        flex-direction: column;
        justify-content: center;
        font-size: 1.5em;
        margin: 0px 10px 0px 10px;
        color: #1f3f70;
    }

    .handle {
        background-color: #e7ebf1;
        width: 45px;
        flex: 0 0 auto;
        display: flex;
    }
    .handle > img {
        margin: auto;
    }
</style>
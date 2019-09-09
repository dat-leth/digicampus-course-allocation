<template>
    <div :id="'schedule_entry_' + entry.id" class="schedule_entry "
         :title="`${entry.start_formatted} - ${entry.end_formatted}\n${entry.title}\n${entry.content}`"
         :style="position" v-on:click="toggleRanking">

        <!-- for safari5 we need to set the height for the dl as well -->
        <dl :style="dlStyle">
            <dt :style="dtStyle">
                {{entry.start_formatted}} - {{entry.end_formatted}}{{ entry.title !== '' ? ', ' + entry.title : ''}}
            </dt>
            <dd>
                {{entry.content}}<br>
            </dd>
        </dl>

        <div class="snatch" style="display: none">
            <div></div>
        </div>

        <div :id="'schedule_icons_' + entry.id" class="schedule_icons" :style="scheduleIconsStyle">
            <strong>{{ rank >= 0 ? rank + 1 : ''}}</strong>
            <strong>{{ this.entry.priority ? Number(this.entry.priority) + 1 : ''}}</strong>
        </div>
    </div>
</template>

<script>
    import {TinyColor} from "@ctrl/tinycolor";

    export default {
        name: "ScheduleEntry",
        props: ['entry'],
        computed: {
            position() {
                let cell_height = 42;
                let cell_steps = cell_height / 60;
                let start = this.entry.start - 800 < 0 ? 0 : this.entry.start - 800;
                let end = this.entry.end - 800;

                let top = Math.floor(cell_height * Math.floor(start / 100) + cell_steps * Math.floor(start % 100));
                let bottom = Math.floor(cell_height * Math.floor(end / 100) + cell_steps * Math.floor(end % 100)) - 1;
                let height = bottom - top;
                let width = 98;
                let left = 0;

                if (this.entry.concurrentEntries.length > 0) {
                    width = width / (this.entry.concurrentEntries.length);
                    left = this.entry.concurrentEntries.findIndex(e => this.entry.id === e.id) * width;
                }

                let style = {top: top + 'px', height: height + 'px', width: width + '%', left: left + '%'};

                if (this.entry.rankable) {
                    style.cursor = 'pointer';
                }
                return style;
            },
            dlStyle() {
                if (this.entry.rankable) {
                    return {
                        height: this.position.height.slice(0, -2) - 2 + 'px',
                        border: '1px solid' + this.entry.color,
                        backgroundColor: new TinyColor(this.entry.color).brighten(5).toString()
                    }
                }
                if (this.entry.other_ranked) {
                    return {
                        height: this.position.height.slice(0, -2) - 2 + 'px',
                        border: '1px solid' + this.entry.color,
                        backgroundColor: new TinyColor(this.entry.color).brighten(5).toString(),
                        opacity: .8
                    }
                }
                return {
                    height: this.position.height.slice(0, -2) - 2 + 'px',
                    border: '1px solid' + new TinyColor(this.entry.color).greyscale().toString(),
                    backgroundColor: new TinyColor(this.entry.color).brighten(5).greyscale().toString(),
                    opacity: .5
                }
            },
            dtStyle() {
                if (this.entry.rankable || this.entry.other_ranked) {
                    return {backgroundColor: this.entry.color}
                }
                return {backgroundColor: new TinyColor(this.entry.color).greyscale().toString()}
            },
            scheduleIconsStyle() {
                let style = {
                    color: '#ffffff',
                    backgroundColor: '#D60000',
                    padding: '0 1em 0 1em',
                };
                if (this.entry.other_ranked) {
                    style.backgroundColor = new TinyColor(this.entry.color).darken(10).toString();
                }
                return style;
            },
            rank() {
                return this.$store.state.ranking.findIndex(itemId => itemId === this.entry.bundle_item);
            }
        },
        methods: {
            toggleRanking() {
                if (this.entry.rankable) {
                    if (this.rank === -1) {
                        this.$store.dispatch("addToRanking", this.entry.bundle_item)
                    } else {
                        this.$store.dispatch("removeFromRanking", this.entry.bundle_item)
                    }
                }
            }
        }
    }
</script>

<style scoped>
</style>
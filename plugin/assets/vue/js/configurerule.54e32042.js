(function(e){function t(t){for(var r,o,a=t[0],u=t[1],c=t[2],d=0,p=[];d<a.length;d++)o=a[d],Object.prototype.hasOwnProperty.call(i,o)&&i[o]&&p.push(i[o][0]),i[o]=0;for(r in u)Object.prototype.hasOwnProperty.call(u,r)&&(e[r]=u[r]);l&&l(t);while(p.length)p.shift()();return s.push.apply(s,c||[]),n()}function n(){for(var e,t=0;t<s.length;t++){for(var n=s[t],r=!0,a=1;a<n.length;a++){var u=n[a];0!==i[u]&&(r=!1)}r&&(s.splice(t--,1),e=o(o.s=n[0]))}return e}var r={},i={configurerule:0},s=[];function o(t){if(r[t])return r[t].exports;var n=r[t]={i:t,l:!1,exports:{}};return e[t].call(n.exports,n,n.exports,o),n.l=!0,n.exports}o.m=e,o.c=r,o.d=function(e,t,n){o.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:n})},o.r=function(e){"undefined"!==typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},o.t=function(e,t){if(1&t&&(e=o(e)),8&t)return e;if(4&t&&"object"===typeof e&&e&&e.__esModule)return e;var n=Object.create(null);if(o.r(n),Object.defineProperty(n,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var r in e)o.d(n,r,function(t){return e[t]}.bind(null,r));return n},o.n=function(e){var t=e&&e.__esModule?function(){return e["default"]}:function(){return e};return o.d(t,"a",t),t},o.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},o.p="/plugins_packages/dat.lethanh@student.uni-augsburg.de/BundleAllocationPlugin/views/";var a=window["webpackJsonp"]=window["webpackJsonp"]||[],u=a.push.bind(a);a.push=t,a=a.slice();for(var c=0;c<a.length;c++)t(a[c]);var l=u;s.push([0,"chunk-vendors"]),n()})({0:function(e,t,n){e.exports=n("3d67")},"020e":function(e,t,n){},"0278":function(e,t,n){},"0845":function(e,t,n){},1583:function(e,t,n){"use strict";var r=n("e032"),i=n.n(r);i.a},"3d67":function(e,t,n){"use strict";n.r(t);n("cadf"),n("551c"),n("f751"),n("097d");var r=n("2b0e"),i=function(){var e=this,t=e.$createElement,n=e._self._c||t;return n("div",{attrs:{id:"app"}},[n("keep-alive",[n(e.currentComponent,e._b({tag:"component"},"component",e.currentProps,!1))],1)],1)},s=[],o=(n("8e6e"),n("456d"),n("bd86")),a=(n("96cf"),n("3b8d")),u=(n("ac6a"),n("5df3"),function(){var e=this,t=e.$createElement;e._self._c;return e._m(0)}),c=[function(){var e=this,t=e.$createElement,n=e._self._c||t;return n("div",{staticClass:"messagebox messagebox_warning"},[n("div",{staticClass:"messagebox_buttons"},[n("a",{staticClass:"close",attrs:{href:"#",title:"Nachrichtenbox schliessen"}},[n("span",[e._v("Nachrichtenbox schliessen")])])]),e._v("\n    Veranstaltungsbezogene Konfigurationen sind erst nach Speichern des Anmeldesets möglich.\n")])}],l={name:"NotSavedWarning"},d=l,p=n("2877"),f=Object(p["a"])(d,u,c,!1,null,"043534ee",null),m=f.exports,h=function(){var e=this,t=e.$createElement,n=e._self._c||t;return n("div",{attrs:{id:"courses"}},[n("h4",[e._v("Schritt "+e._s(e.step+1)+": Teilnehmendenanzahl konfigurieren")]),e._m(0),n("table",{staticClass:"default"},[e._m(1),e._m(2),n("tbody",e._l(e.courses,function(t,r){return n("tr",{key:r},[n("td",[n("a",{attrs:{href:"/dispatch.php/course/details/index/"+t.seminar_id,"data-dialog":""}},[n("img",{staticClass:"icon-role-inactive icon-shape-info-circle",attrs:{title:"Veranstaltungsdetails aufrufen",src:"/assets/images/icons/grey/info-circle.svg",alt:"Veranstaltungsdetails aufrufen",width:"16",height:"16"}})])]),n("td",[e._v(e._s(t.name))]),n("td",[n("ul",e._l(t.cycles,function(t,r){return n("li",{key:r},[e._v("\n                        "+e._s(e._f("weekday")(t.weekday))+", "+e._s(e._f("time")(t.start_time))+" - "+e._s(e._f("time")(t.end_time))+"\n                    ")])}),0)]),n("td",[n("input",{attrs:{type:"number",min:"0",placeholder:"0"},domProps:{value:t.capacity},on:{input:function(t){return e.updateCapacity(t,r)}}})])])}),0)]),this.$store.state.step>0?n("button",{staticClass:"button",on:{click:function(t){return t.preventDefault(),e.prevStep(t)}}},[e._v("Zurück")]):e._e(),this.$store.state.components.length-1>this.$store.state.step?n("button",{staticClass:"button",on:{click:function(t){return t.preventDefault(),e.nextStep(t)}}},[e._v("\n        Weiter\n    ")]):e._e()])},g=[function(){var e=this,t=e.$createElement,n=e._self._c||t;return n("p",[n("strong",[e._v("Hinweis:")]),e._v(" Bei Änderungen an der Veranstaltungszuordnung des Anmeldesets muss die\n        Anmelderegel stets neukonfiguriert werden. Ansonsten kann es zur fehlerhaften Prioritätenerhebung und\n        Zuteilung kommen.\n    ")])},function(){var e=this,t=e.$createElement,n=e._self._c||t;return n("thead",[n("tr",[n("th"),n("th",[e._v("Veranstaltung")]),n("th",[e._v("Regelmäßige Termine")]),n("th",[e._v("max. Teilnehmendenanzahl")])])])},function(){var e=this,t=e.$createElement,n=e._self._c||t;return n("colgroup",[n("col",{attrs:{width:"18"}}),n("col"),n("col"),n("col")])}],b=n("2f62"),_=n("c1df"),v=n.n(_),j=n("bc3a"),y=n.n(j);function k(e,t){var n=Object.keys(e);if(Object.getOwnPropertySymbols){var r=Object.getOwnPropertySymbols(e);t&&(r=r.filter(function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable})),n.push.apply(n,r)}return n}function I(e){for(var t=1;t<arguments.length;t++){var n=null!=arguments[t]?arguments[t]:{};t%2?k(n,!0).forEach(function(t){Object(o["a"])(e,t,n[t])}):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(n)):k(n).forEach(function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(n,t))})}return e}var O={name:"Courses",computed:I({},Object(b["c"])(["courses","step","courseSetId"])),methods:{nextStep:function(){if(document.querySelector("#ruleform").reportValidity()){var e=this.courses.map(function(e){return{seminar_id:e.seminar_id,capacity:e.capacity}});y.a.post("/plugins.php/bundleallocationplugin/config/courses_capacity/",e).then(this.$store.dispatch("nextStep")).catch(function(e){return console.log(e)})}},prevStep:function(){this.$store.dispatch("prevStep")},updateCapacity:function(e,t){this.$store.dispatch("setCourseCapacity",{index:t,capacity:parseFloat(e.target.value)})}},filters:{weekday:function(e){return v()().isoWeekday(e).format("dddd")},time:function(e){return v()(e,"HH:mm:ss").format("HH:mm")}}},w=O,S=(n("6600"),Object(p["a"])(w,h,g,!1,null,"103995a1",null)),x=S.exports,P=function(){var e=this,t=e.$createElement,n=e._self._c||t;return n("div",{attrs:{id:"ranking_groups"}},[n("h4",[e._v("Schritt "+e._s(e.step+1)+": Zuteilungsgruppen konfigurieren")]),n("p",[e._v("\n        Für jede Zuteilungsgruppe kann jeweils unabhängig von den anderen Gruppen Prioritäten zu Veranstaltungen\n        abgegeben werden. Es wird jedem Studierenden ein (1) Kurs pro Gruppe zugeteilt. Parallel stattfindende\n        Veranstaltungen können für die Prioritätenerhebung zusammengefasst werden.\n    ")]),n("section",{staticClass:"contentbox"},[0===e.rankingGroups.length?n("div",{staticClass:"messagebox messagebox_info"},[e._v("\n            Keine Zuteilungsgruppe vorhanden.\n        ")]):e._e(),e._l(e.rankingGroups,function(e,t){return n("ranking-group",{key:e.group_id,attrs:{index:t,group:e}})})],2),n("button",{staticClass:"button",on:{click:function(t){return t.preventDefault(),e.addRankingGroup(t)}}},[e._v("Neue Zuteilungsgruppe erstellen")]),n("br"),this.$store.state.step>0?n("button",{staticClass:"button",on:{click:function(t){return t.preventDefault(),e.prevStep(t)}}},[e._v("Zurück")]):e._e(),this.$store.state.components.length-1>this.$store.state.step?n("button",{staticClass:"button",on:{click:function(t){return t.preventDefault(),e.nextStep(t)}}},[e._v("\n        Weiter\n    ")]):e._e()])},C=[],G=function(){var e=this,t=e.$createElement,n=e._self._c||t;return n("article",{},[n("header",[n("h1",[n("a",{attrs:{href:"#"}},[e._v("\n                "+e._s(e.group.group_name)+"\n            ")])]),n("nav",[n("a",{attrs:{"data-dialog":"size=auto;"},on:{click:function(t){return t.preventDefault(),e.deleteGroup(t)}}},[n("svg",{attrs:{width:"16",height:"16",xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 16 16","shape-rendering":"geometricPrecision",fill:"#24437c"}},[n("path",{attrs:{d:"M9.759 2.438V1.03H6.24v1.408H2.717v1.917h10.565V2.438H9.759zM3.661 14.97h8.645V5.388H3.661v9.582zm6.163-8.075h1.016v6.567H9.824V6.895zm-2.35 0h1.017v6.567H7.474V6.895zm-2.349 0h1.016v6.567H5.125V6.895z"}})])])])]),n("section",[n("label",{staticClass:"required",attrs:{for:"name"}},[e._v("Name")]),n("div",{staticClass:"length-hint-wrapper",staticStyle:{width:"666.6px"}},[e._m(0),n("input",{attrs:{type:"text",id:"name",size:"75",maxlength:"255",required:"","aria-required":"true"},domProps:{value:e.group.group_name},on:{input:e.updateGroupName}})]),n("label",{staticClass:"required"},[e._v("Minimale Anzahl einzureichende Prioritäten")]),n("input",{attrs:{type:"number",min:"0",max:e.bundleItems.length},domProps:{value:e.group.min_amount_prios},on:{input:e.updateMinAmountPrios}}),n("label",{staticClass:"required"},[e._v("Zugeordnete Veranstaltungen")]),n("table",{staticClass:"default"},[e._m(1),e._m(2),n("tbody",[0===e.bundleItems.length?n("tr",[n("td"),e._m(3)]):e._e(),e._l(e.bundleItems,function(t){return e._l(e.courseDetails(t.seminar_ids),function(r,i){return n("tr",{key:r.seminar_id},[0===i?n("td",{attrs:{rowspan:t.seminar_ids.length}},[n("input",{directives:[{name:"model",rawName:"v-model",value:e.checkboxed[t.item_id],expression:"checkboxed[bundle.item_id]"}],attrs:{type:"checkbox"},domProps:{checked:Array.isArray(e.checkboxed[t.item_id])?e._i(e.checkboxed[t.item_id],null)>-1:e.checkboxed[t.item_id]},on:{change:function(n){var r=e.checkboxed[t.item_id],i=n.target,s=!!i.checked;if(Array.isArray(r)){var o=null,a=e._i(r,o);i.checked?a<0&&e.$set(e.checkboxed,t.item_id,r.concat([o])):a>-1&&e.$set(e.checkboxed,t.item_id,r.slice(0,a).concat(r.slice(a+1)))}else e.$set(e.checkboxed,t.item_id,s)}}})]):e._e(),n("td",{style:{borderLeft:t.seminar_ids.length>1?"3px solid #e7ebf1":"3px none"}},[e._v(e._s(r.name))]),n("td",[n("ul",e._l(r.cycles,function(t,r){return n("li",{key:r},[e._v("\n                                "+e._s(e._f("weekday")(t.weekday))+", "+e._s(e._f("time")(t.start_time))+" - "+e._s(e._f("time")(t.end_time))+"\n                            ")])}),0)]),n("td",[e._v(e._s(r.capacity))])])})})],2),n("tfoot",[n("tr",[n("td",{attrs:{colspan:"4"}},[n("select",{directives:[{name:"model",rawName:"v-model",value:e.action,expression:"action"}],staticClass:"select-action",attrs:{disabled:0===e.selectedBundleItems.length},on:{change:function(t){var n=Array.prototype.filter.call(t.target.options,function(e){return e.selected}).map(function(e){var t="_value"in e?e._value:e.value;return t});e.action=t.target.multiple?n:n[0]}}},[n("option",{attrs:{selected:"",disabled:"",value:""}},[e._v("Aktionen")]),e.selectedBundleItems.length>0?n("option",{attrs:{value:"delete"}},[e._v("Zuordnung löschen")]):e._e(),e.selectedBundleItems.length>1?n("option",{attrs:{value:"merge"}},[e._v("Zusammenfassen")]):e._e(),e.splittableBundleItem?n("option",{attrs:{value:"split"}},[e._v("Trennen")]):e._e()]),n("button",{staticClass:"button",on:{click:function(t){return t.preventDefault(),e.delegateAction(t)}}},[e._v("Ausführen")])])])])]),e._m(4),n("table",{staticClass:"default"},[e._m(5),e._m(6),n("tbody",[0===e.notAssignedCourses.length?n("tr",[n("td"),e._m(7)]):e._e(),e._l(e.notAssignedCourses,function(t){return n("tr",{key:t.seminar_id},[n("td",[n("button",{staticClass:"add-button",on:{click:function(n){return n.preventDefault(),e.addCourseToGroup(t.seminar_id)}}},[n("svg",{attrs:{width:"16",height:"16",xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 16 16","shape-rendering":"geometricPrecision",fill:"#24437c"}},[n("path",{attrs:{d:"M8 1.001a7 7 0 1 0 .003 14 7 7 0 0 0-.003-14zm4.016 8.024H9.023l.002 2.943-2.048.001.001-2.943H3.984l-.001-2.05h2.994V4l2.047-.001v2.976l2.993.001-.001 2.049z"}})])])]),n("td",[e._v(e._s(t.name))]),n("td",[n("ul",e._l(t.cycles,function(t,r){return n("li",{key:r},[e._v("\n                            "+e._s(e._f("weekday")(t.weekday))+", "+e._s(e._f("time")(t.start_time))+" - "+e._s(e._f("time")(t.end_time))+"\n                        ")])}),0)]),n("td",[e._v(e._s(t.capacity))])])})],2)])])])},z=[function(){var e=this,t=e.$createElement,n=e._self._c||t;return n("div",{staticClass:"length-hint",staticStyle:{display:"none"}},[e._v("\n                Zeichen verbleibend: "),n("span",{staticClass:"length-hint-counter"},[e._v("255")])])},function(){var e=this,t=e.$createElement,n=e._self._c||t;return n("thead",[n("tr",[n("th"),n("th",[e._v("Veranstaltung")]),n("th",[e._v("Regelmäßige Termine")]),n("th",[e._v("max. Teilnehmendenanzahl")])])])},function(){var e=this,t=e.$createElement,n=e._self._c||t;return n("colgroup",[n("col",{attrs:{width:"35"}}),n("col"),n("col"),n("col")])},function(){var e=this,t=e.$createElement,n=e._self._c||t;return n("td",{attrs:{colspan:"3"}},[n("em",[e._v("keine zugeordneten Veranstaltungen")])])},function(){var e=this,t=e.$createElement,n=e._self._c||t;return n("label",[n("strong",[e._v("Keiner Zuteilungsgruppe zugeordnet")])])},function(){var e=this,t=e.$createElement,n=e._self._c||t;return n("thead",[n("tr",[n("th"),n("th",[e._v("Veranstaltung")]),n("th",[e._v("Regelmäßige Termine")]),n("th",[e._v("max. Teilnehmendenanzahl")])])])},function(){var e=this,t=e.$createElement,n=e._self._c||t;return n("colgroup",[n("col",{attrs:{width:"35"}}),n("col"),n("col"),n("col")])},function(){var e=this,t=e.$createElement,n=e._self._c||t;return n("td",{attrs:{colspan:"3"}},[n("em",[e._v("keine zuzuordnenden Veranstaltungen")])])}],B=(n("6762"),n("75fc"));n("7f7f"),n("7514"),n("55dd");function E(e,t){var n=Object.keys(e);if(Object.getOwnPropertySymbols){var r=Object.getOwnPropertySymbols(e);t&&(r=r.filter(function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable})),n.push.apply(n,r)}return n}function D(e){for(var t=1;t<arguments.length;t++){var n=null!=arguments[t]?arguments[t]:{};t%2?E(n,!0).forEach(function(t){Object(o["a"])(e,t,n[t])}):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(n)):E(n).forEach(function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(n,t))})}return e}var A={name:"RankingGroup",data:function(){return{checkboxed:{},action:""}},props:["index","group"],computed:D({},Object(b["b"])(["courses"]),{bundleItems:function(){var e=this,t=this.$store.getters.bundleItems.filter(function(t){return t.group_id===e.group.group_id});return t.forEach(function(t){t.seminar_ids.sort(function(t,n){var r=e.courses.find(function(e){return e.seminar_id===t}),i=e.courses.find(function(e){return e.seminar_id===n});return r.name.localeCompare(i.name)})}),t.sort(function(t,n){var r=e.courses.find(function(e){return e.seminar_id===t.seminar_ids[0]}),i=e.courses.find(function(e){return e.seminar_id===n.seminar_ids[0]});return r.name.localeCompare(i.name)}),t},notAssignedCourses:function(){var e=[];return this.$store.getters.bundleItems.forEach(function(t){e.push.apply(e,Object(B["a"])(t.seminar_ids))}),this.courses.filter(function(t){return!e.includes(t.seminar_id)})},selectedBundleItems:function(){var e=this;return Object.keys(this.checkboxed).filter(function(t){return!0===e.checkboxed[t]})},splittableBundleItem:function(){var e=this;if(1===this.selectedBundleItems.length){var t=this.bundleItems.find(function(t){return t.item_id===e.selectedBundleItems[0]});return t.seminar_ids.length>1}return!1}}),methods:{deleteGroup:function(){this.$store.dispatch("removeRankingGroup",this.index)},courseDetails:function(e){var t=this;return e.map(function(e){return t.courses.find(function(t){return t.seminar_id===e})})},updateGroupName:function(e){this.$store.dispatch("setGroupName",{id:this.group.group_id,name:e.target.value})},updateMinAmountPrios:function(e){this.$store.dispatch("setGroupMinAmountPrio",{id:this.group.group_id,amount:parseFloat(e.target.value)})},addCourseToGroup:function(e){this.$store.dispatch("addBundleItem",{groupId:this.group.group_id,seminarIds:[e]})},delegateAction:function(){var e=this;"delete"===this.action?this.selectedBundleItems.forEach(function(t){return e.$store.dispatch("deleteBundleItem",t)}):"merge"===this.action?this.$store.dispatch("mergeBundleItems",this.selectedBundleItems):"split"===this.action&&this.$store.dispatch("splitBundleItems",{groupId:this.group.group_id,itemId:this.selectedBundleItems[0]}),this.checkboxed={},this.action=""}},filters:{weekday:function(e){return v()().isoWeekday(e).format("dddd")},time:function(e){return v()(e,"HH:mm:ss").format("HH:mm")}}},R=A,N=(n("fd24"),Object(p["a"])(R,G,z,!1,null,"4422f09e",null)),V=N.exports;function H(e,t){var n=Object.keys(e);if(Object.getOwnPropertySymbols){var r=Object.getOwnPropertySymbols(e);t&&(r=r.filter(function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable})),n.push.apply(n,r)}return n}function T(e){for(var t=1;t<arguments.length;t++){var n=null!=arguments[t]?arguments[t]:{};t%2?H(n,!0).forEach(function(t){Object(o["a"])(e,t,n[t])}):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(n)):H(n).forEach(function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(n,t))})}return e}var M={name:"RankingGroupList",components:{RankingGroup:V},computed:T({},Object(b["c"])(["step","rankingGroups","ruleId"])),methods:{nextStep:function(){var e=this;y.a.post("/plugins.php/bundleallocationplugin/config/ranking_groups/"+this.ruleId,this.rankingGroups).then(function(){return e.$store.dispatch("nextStep")}).catch(function(e){return console.log(e)})},prevStep:function(){this.$store.dispatch("prevStep")},addRankingGroup:function(){this.$store.dispatch("addRankingGroup")}}},Z=M,L=(n("fc38"),Object(p["a"])(Z,P,C,!1,null,"2e41c60c",null)),F=L.exports,q=function(){var e=this,t=e.$createElement,n=e._self._c||t;return n("div",{attrs:{id:"finished"}},[n("h4",[e._v("Fertig!")]),n("p",[e._v("Die Anmelderegel wurde erfolgreich konfiguriert. Sie können den Dialog über 'Speichern' nun schließen.")]),this.$store.state.step>0?n("button",{staticClass:"button",on:{click:function(t){return t.preventDefault(),e.prevStep(t)}}},[e._v("Zurück")]):e._e()])},W=[],J={name:"Finished",methods:{prevStep:function(){this.$store.dispatch("prevStep")}}},K=J,U=Object(p["a"])(K,q,W,!1,null,"5b8d2d13",null),Q=U.exports;function X(e,t){var n=Object.keys(e);if(Object.getOwnPropertySymbols){var r=Object.getOwnPropertySymbols(e);t&&(r=r.filter(function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable})),n.push.apply(n,r)}return n}function Y(e){for(var t=1;t<arguments.length;t++){var n=null!=arguments[t]?arguments[t]:{};t%2?X(n,!0).forEach(function(t){Object(o["a"])(e,t,n[t])}):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(n)):X(n).forEach(function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(n,t))})}return e}var ee={name:"app",components:{Courses:x,RankingGroupList:F,NotSavedWarning:m,Finished:Q},created:function(){var e=this;if(this.$store.dispatch("setRuleId",BUNDLEALLOCATION.rule_id),this.$store.dispatch("setCourseSetId",BUNDLEALLOCATION.courseset_id),""!==this.$store.getters.courseSetId){var t=[];t.push(this.$store.dispatch("fetchCourses")),t.push(new Promise(function(t){e.$store.dispatch("fetchRankingGroups").then(function(){return e.$store.dispatch("fetchBundleItems")}).then(function(){return t()})})),Promise.all(t).then(function(){e.$store.dispatch("setInitial")}).catch(function(e){return console.log(e)})}},mounted:function(){var e=this;Object(a["a"])(regeneratorRuntime.mark(function t(){return regeneratorRuntime.wrap(function(t){while(1)switch(t.prev=t.next){case 0:setTimeout(function(){$("button.ui-dialog-titlebar-close").on("click",function(){e.sendInitialState()}),$("div.ui-widget-overlay.ui-front").on("click",function(){e.sendInitialState()}),$("button.cancel.ui-button.ui-corner-all.ui-widget").on("click",function(){e.sendInitialState()}),$("button.accept").on("click",function(){e.sendCurrentState()})},1);case 1:case"end":return t.stop()}},t)}))()},computed:Y({},Object(b["c"])(["courseSetId","initialState","courses","ruleId","rankingGroups","bundleItems"]),{currentComponent:function(){return $(".hidden-alert").is(":visible")||""===this.$store.state.courseSetId?"NotSavedWarning":this.$store.state.components[this.$store.state.step]},currentProps:function(){return{}}}),methods:{sendInitialState:function(){var e=this;if(""!==this.courseSetId){var t=this.initialState.courses.map(function(e){return{seminar_id:e.seminar_id,capacity:e.capacity}});y.a.post("/plugins.php/bundleallocationplugin/config/courses_capacity/",t).catch(function(e){return console.log(e)}),y.a.post("/plugins.php/bundleallocationplugin/config/ranking_groups/"+this.initialState.ruleId,this.initialState.rankingGroups).catch(function(e){return console.log(e)}),this.initialState.rankingGroups.forEach(function(t){y.a.post("/plugins.php/bundleallocationplugin/config/bundleitems/"+t.group_id,e.initialState.bundleItems.filter(function(e){return e.group_id===t.group_id}))})}},sendCurrentState:function(){var e=this;if(""!==this.courseSetId){var t=this.courses.map(function(e){return{seminar_id:e.seminar_id,capacity:e.capacity}});y.a.post("/plugins.php/bundleallocationplugin/config/courses_capacity/",t).catch(function(e){return console.log(e)}),y.a.post("/plugins.php/bundleallocationplugin/config/ranking_groups/"+this.ruleId,this.rankingGroups).catch(function(e){return console.log(e)}),this.rankingGroups.forEach(function(t){y.a.post("/plugins.php/bundleallocationplugin/config/bundleitems/"+t.group_id,e.bundleItems.filter(function(e){return e.group_id===t.group_id})).catch(function(e){return console.log(e)})})}}}},te=ee,ne=(n("1583"),Object(p["a"])(te,i,s,!1,null,null,null)),re=ne.exports;r["a"].use(b["a"]);var ie=new b["a"].Store({state:{ruleId:"",courseSetId:"",step:0,components:["Courses","RankingGroupList","Finished"],courses:{},rankingGroups:[],bundleItems:[],initialState:{}},getters:{ruleId:function(e){return e.ruleId},courseSetId:function(e){return e.courseSetId},courses:function(e){return e.courses},rankingGroups:function(e){return e.rankingGroups},rankingGroupForId:function(e){return function(t){return e.rankingGroups.find(function(e){return e.id===t})}},bundleItems:function(e){return e.bundleItems}},mutations:{nextStep:function(e){e.step++},prevStep:function(e){e.step--},setRuleId:function(e,t){e.ruleId=t},setCourseSetId:function(e,t){e.courseSetId=t},setCourses:function(e,t){e.courses=t},setCourseCapacity:function(e,t){var n=t.index,r=t.capacity;e.courses[n].capacity=r},setRankingGroups:function(e,t){e.rankingGroups=t},setBundleItems:function(e,t){e.bundleItems=t},addRankingGroup:function(e,t){e.rankingGroups.push(t)},removeRankingGroup:function(e,t){e.rankingGroups.splice(t,1)},setGroupName:function(e,t){var n=t.id,r=t.name;e.rankingGroups.find(function(e){return e.group_id===n}).group_name=r},setGroupMinAmountPrio:function(e,t){var n=t.id,r=t.amount;e.rankingGroups.find(function(e){return e.group_id===n}).min_amount_prios=r},addBundleItem:function(e,t){var n=t.groupId,r=t.itemId,i=t.seminarIds;e.bundleItems.push({item_id:r,group_id:n,seminar_ids:i,start_time:null,end_time:null,weekday:null})},deleteBundleItem:function(e,t){e.bundleItems=e.bundleItems.filter(function(e){return e.item_id!==t})},mergeBundleItems:function(e,t){var n,r=t.item,i=t.seminarIds;(n=r.seminar_ids).push.apply(n,Object(B["a"])(i))},setInitial:function(e,t){e.initialState=JSON.parse(JSON.stringify(t))}},actions:{setInitial:function(e){var t=e.commit,n=e.state;t("setInitial",n)},nextStep:function(e){e.commit("nextStep")},prevStep:function(e){e.commit("prevStep")},setRuleId:function(e,t){e.commit("setRuleId",t)},setCourseSetId:function(e,t){var n=e.commit;n("setCourseSetId",t)},setCourseCapacity:function(e,t){var n=e.commit,r=t.index,i=t.capacity;n("setCourseCapacity",{index:r,capacity:i})},setGroupName:function(e,t){var n=e.commit,r=t.id,i=t.name;n("setGroupName",{id:r,name:i})},setGroupMinAmountPrio:function(e,t){var n=e.commit,r=t.id,i=t.amount;n("setGroupMinAmountPrio",{id:r,amount:i})},addRankingGroup:function(e){var t=e.commit,n=e.state;y.a.post("/plugins.php/bundleallocationplugin/config/create_ranking_group/"+n.ruleId).then(function(e){t("addRankingGroup",{group_id:e.data.group_id,rule_id:n.ruleId,group_name:"Neue Zuteilungsgruppe",min_amount_prios:0})}).catch(function(e){console.log(e)})},removeRankingGroup:function(e,t){var n=e.commit,r=e.state,i=r.rankingGroups[t].group_id;y.a.delete("/plugins.php/bundleallocationplugin/config/delete_ranking_group/"+i).then(function(){n("setBundleItems",r.bundleItems.filter(function(e){return e.group_id!==i})),n("removeRankingGroup",t)}).catch(function(e){return console.log(e)})},addBundleItem:function(e,t){var n=e.commit,r=t.groupId,i=t.seminarIds;return new Promise(function(e){y.a.post("/plugins.php/bundleallocationplugin/config/create_bundleitem/"+r,{seminar_ids:i,start_time:null,end_time:null,weekday:null}).then(function(t){n("addBundleItem",{itemId:t.data.item_id,groupId:r,seminarIds:i}),e()}).catch(function(e){console.log(e)})})},deleteBundleItem:function(e,t){var n=e.commit;return new Promise(function(e){y.a.delete("/plugins.php/bundleallocationplugin/config/delete_bundleitem/"+t).then(function(){n("deleteBundleItem",t),e()}).catch(function(e){return console.log(e)})})},mergeBundleItems:function(e,t){for(var n=e.dispatch,r=e.commit,i=e.state,s=i.bundleItems.find(function(e){return e.item_id===t[0]}),o=[],a=function(e){var n=i.bundleItems.find(function(n){return n.item_id===t[e]});o.push.apply(o,Object(B["a"])(n.seminar_ids))},u=1;u<t.length;u++)a(u);var c=[];for(u=1;u<t.length;u++)c.push(n("deleteBundleItem",t[u]));Promise.all(c).then(function(){r("mergeBundleItems",{item:s,seminarIds:o}),y.a.post("/plugins.php/bundleallocationplugin/config/update_bundleitem/"+t[0],i.bundleItems.find(function(e){return e.item_id===t[0]})).catch(function(e){return console.log(e)})})},splitBundleItems:function(e,t){var n=e.dispatch,r=e.state,i=t.groupId,s=t.itemId,o=r.bundleItems.find(function(e){return e.item_id===s}).seminar_ids;n("deleteBundleItem",s).then(function(){o.forEach(function(e){return n("addBundleItem",{groupId:i,seminarIds:[e]})})})},fetchCourses:function(e){var t=e.commit,n=e.state;return new Promise(function(e){y.a.get("/plugins.php/bundleallocationplugin/config/get_courses/"+n.courseSetId).then(function(n){t("setCourses",n.data),e()}).catch(function(e){console.log(e)})})},fetchRankingGroups:function(e){var t=e.commit,n=e.state;return new Promise(function(e){y.a.get("/plugins.php/bundleallocationplugin/config/ranking_groups/"+n.ruleId).then(function(n){t("setRankingGroups",n.data),e()})})},fetchBundleItems:function(e){var t=e.commit,n=e.state,r=[];return n.rankingGroups.forEach(function(e){r.push(y.a.get("/plugins.php/bundleallocationplugin/config/bundleitems/"+e.group_id).then(function(e){t("setBundleItems",[].concat(Object(B["a"])(n.bundleItems),Object(B["a"])(e.data)))}).catch(function(e){console.log(e)}))}),Promise.all(r)}}});r["a"].config.productionTip=!1,new r["a"]({store:ie,render:function(e){return e(re)}}).$mount("#app")},4678:function(e,t,n){var r={"./af":"2bfb","./af.js":"2bfb","./ar":"8e73","./ar-dz":"a356","./ar-dz.js":"a356","./ar-kw":"423e","./ar-kw.js":"423e","./ar-ly":"1cfd","./ar-ly.js":"1cfd","./ar-ma":"0a84","./ar-ma.js":"0a84","./ar-sa":"8230","./ar-sa.js":"8230","./ar-tn":"6d83","./ar-tn.js":"6d83","./ar.js":"8e73","./az":"485c","./az.js":"485c","./be":"1fc1","./be.js":"1fc1","./bg":"84aa","./bg.js":"84aa","./bm":"a7fa","./bm.js":"a7fa","./bn":"9043","./bn.js":"9043","./bo":"d26a","./bo.js":"d26a","./br":"6887","./br.js":"6887","./bs":"2554","./bs.js":"2554","./ca":"d716","./ca.js":"d716","./cs":"3c0d","./cs.js":"3c0d","./cv":"03ec","./cv.js":"03ec","./cy":"9797","./cy.js":"9797","./da":"0f14","./da.js":"0f14","./de":"b469","./de-at":"b3eb","./de-at.js":"b3eb","./de-ch":"bb71","./de-ch.js":"bb71","./de.js":"b469","./dv":"598a","./dv.js":"598a","./el":"8d47","./el.js":"8d47","./en-SG":"cdab","./en-SG.js":"cdab","./en-au":"0e6b","./en-au.js":"0e6b","./en-ca":"3886","./en-ca.js":"3886","./en-gb":"39a6","./en-gb.js":"39a6","./en-ie":"e1d3","./en-ie.js":"e1d3","./en-il":"7333","./en-il.js":"7333","./en-nz":"6f50","./en-nz.js":"6f50","./eo":"65db","./eo.js":"65db","./es":"898b","./es-do":"0a3c","./es-do.js":"0a3c","./es-us":"55c9","./es-us.js":"55c9","./es.js":"898b","./et":"ec18","./et.js":"ec18","./eu":"0ff2","./eu.js":"0ff2","./fa":"8df4","./fa.js":"8df4","./fi":"81e9","./fi.js":"81e9","./fo":"0721","./fo.js":"0721","./fr":"9f26","./fr-ca":"d9f8","./fr-ca.js":"d9f8","./fr-ch":"0e49","./fr-ch.js":"0e49","./fr.js":"9f26","./fy":"7118","./fy.js":"7118","./ga":"5120","./ga.js":"5120","./gd":"f6b4","./gd.js":"f6b4","./gl":"8840","./gl.js":"8840","./gom-latn":"0caa","./gom-latn.js":"0caa","./gu":"e0c5","./gu.js":"e0c5","./he":"c7aa","./he.js":"c7aa","./hi":"dc4d","./hi.js":"dc4d","./hr":"4ba9","./hr.js":"4ba9","./hu":"5b14","./hu.js":"5b14","./hy-am":"d6b6","./hy-am.js":"d6b6","./id":"5038","./id.js":"5038","./is":"0558","./is.js":"0558","./it":"6e98","./it-ch":"6f12","./it-ch.js":"6f12","./it.js":"6e98","./ja":"079e","./ja.js":"079e","./jv":"b540","./jv.js":"b540","./ka":"201b","./ka.js":"201b","./kk":"6d79","./kk.js":"6d79","./km":"e81d","./km.js":"e81d","./kn":"3e92","./kn.js":"3e92","./ko":"22f8","./ko.js":"22f8","./ku":"2421","./ku.js":"2421","./ky":"9609","./ky.js":"9609","./lb":"440c","./lb.js":"440c","./lo":"b29d","./lo.js":"b29d","./lt":"26f9","./lt.js":"26f9","./lv":"b97c","./lv.js":"b97c","./me":"293c","./me.js":"293c","./mi":"688b","./mi.js":"688b","./mk":"6909","./mk.js":"6909","./ml":"02fb","./ml.js":"02fb","./mn":"958b","./mn.js":"958b","./mr":"39bd","./mr.js":"39bd","./ms":"ebe4","./ms-my":"6403","./ms-my.js":"6403","./ms.js":"ebe4","./mt":"1b45","./mt.js":"1b45","./my":"8689","./my.js":"8689","./nb":"6ce3","./nb.js":"6ce3","./ne":"3a39","./ne.js":"3a39","./nl":"facd","./nl-be":"db29","./nl-be.js":"db29","./nl.js":"facd","./nn":"b84c","./nn.js":"b84c","./pa-in":"f3ff","./pa-in.js":"f3ff","./pl":"8d57","./pl.js":"8d57","./pt":"f260","./pt-br":"d2d4","./pt-br.js":"d2d4","./pt.js":"f260","./ro":"972c","./ro.js":"972c","./ru":"957c","./ru.js":"957c","./sd":"6784","./sd.js":"6784","./se":"ffff","./se.js":"ffff","./si":"eda5","./si.js":"eda5","./sk":"7be6","./sk.js":"7be6","./sl":"8155","./sl.js":"8155","./sq":"c8f3","./sq.js":"c8f3","./sr":"cf1e","./sr-cyrl":"13e9","./sr-cyrl.js":"13e9","./sr.js":"cf1e","./ss":"52bd","./ss.js":"52bd","./sv":"5fbd","./sv.js":"5fbd","./sw":"74dc","./sw.js":"74dc","./ta":"3de5","./ta.js":"3de5","./te":"5cbb","./te.js":"5cbb","./tet":"576c","./tet.js":"576c","./tg":"3b1b","./tg.js":"3b1b","./th":"10e8","./th.js":"10e8","./tl-ph":"0f38","./tl-ph.js":"0f38","./tlh":"cf75","./tlh.js":"cf75","./tr":"0e81","./tr.js":"0e81","./tzl":"cf51","./tzl.js":"cf51","./tzm":"c109","./tzm-latn":"b53d","./tzm-latn.js":"b53d","./tzm.js":"c109","./ug-cn":"6117","./ug-cn.js":"6117","./uk":"ada2","./uk.js":"ada2","./ur":"5294","./ur.js":"5294","./uz":"2e8c","./uz-latn":"010e","./uz-latn.js":"010e","./uz.js":"2e8c","./vi":"2921","./vi.js":"2921","./x-pseudo":"fd7e","./x-pseudo.js":"fd7e","./yo":"7f33","./yo.js":"7f33","./zh-cn":"5c3a","./zh-cn.js":"5c3a","./zh-hk":"49ab","./zh-hk.js":"49ab","./zh-tw":"90ea","./zh-tw.js":"90ea"};function i(e){var t=s(e);return n(t)}function s(e){if(!n.o(r,e)){var t=new Error("Cannot find module '"+e+"'");throw t.code="MODULE_NOT_FOUND",t}return r[e]}i.keys=function(){return Object.keys(r)},i.resolve=s,e.exports=i,i.id="4678"},6600:function(e,t,n){"use strict";var r=n("0845"),i=n.n(r);i.a},e032:function(e,t,n){},fc38:function(e,t,n){"use strict";var r=n("0278"),i=n.n(r);i.a},fd24:function(e,t,n){"use strict";var r=n("020e"),i=n.n(r);i.a}});
//# sourceMappingURL=configurerule.54e32042.js.map
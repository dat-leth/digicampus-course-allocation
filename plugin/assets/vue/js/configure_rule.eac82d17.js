(function(e){function t(t){for(var n,i,a=t[0],u=t[1],c=t[2],d=0,p=[];d<a.length;d++)i=a[d],Object.prototype.hasOwnProperty.call(o,i)&&o[i]&&p.push(o[i][0]),o[i]=0;for(n in u)Object.prototype.hasOwnProperty.call(u,n)&&(e[n]=u[n]);l&&l(t);while(p.length)p.shift()();return s.push.apply(s,c||[]),r()}function r(){for(var e,t=0;t<s.length;t++){for(var r=s[t],n=!0,a=1;a<r.length;a++){var u=r[a];0!==o[u]&&(n=!1)}n&&(s.splice(t--,1),e=i(i.s=r[0]))}return e}var n={},o={configure_rule:0},s=[];function i(t){if(n[t])return n[t].exports;var r=n[t]={i:t,l:!1,exports:{}};return e[t].call(r.exports,r,r.exports,i),r.l=!0,r.exports}i.m=e,i.c=n,i.d=function(e,t,r){i.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:r})},i.r=function(e){"undefined"!==typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},i.t=function(e,t){if(1&t&&(e=i(e)),8&t)return e;if(4&t&&"object"===typeof e&&e&&e.__esModule)return e;var r=Object.create(null);if(i.r(r),Object.defineProperty(r,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var n in e)i.d(r,n,function(t){return e[t]}.bind(null,n));return r},i.n=function(e){var t=e&&e.__esModule?function(){return e["default"]}:function(){return e};return i.d(t,"a",t),t},i.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},i.p="/plugins_packages/uaux/BundleAllocationPlugin/views/";var a=window["webpackJsonp"]=window["webpackJsonp"]||[],u=a.push.bind(a);a.push=t,a=a.slice();for(var c=0;c<a.length;c++)t(a[c]);var l=u;s.push([0,"chunk-vendors"]),r()})({0:function(e,t,r){e.exports=r("4cb3")},"30f3":function(e,t,r){"use strict";var n=r("46c3"),o=r.n(n);o.a},"46c3":function(e,t,r){},"4cb3":function(e,t,r){"use strict";r.r(t);r("cadf"),r("551c"),r("f751"),r("097d");var n=r("2b0e"),o=function(){var e=this,t=e.$createElement,r=e._self._c||t;return r("div",{attrs:{id:"app"}},[r("DistDateTime"),r("CourseCapacity"),r("RankingGroupList")],1)},s=[],i=function(){var e=this,t=e.$createElement,r=e._self._c||t;return r("div",{attrs:{id:"datetime"}},[r("h3",[e._v("Schritt 1: Verteilzeitpunkt festlegen")]),r("label",{staticClass:"col-1"},[r("span",{staticClass:"required"},[e._v("Datum")]),r("input",{staticClass:"size-s no-hint",attrs:{type:"text",name:"distributiondate",id:"distributiondate",placeholder:"tt.mm.jjjj",required:""},domProps:{value:e.rule.dist_date}})]),r("label",{staticClass:"col-1"},[r("span",{staticClass:"required"},[e._v("Uhrzeit")]),r("input",{staticClass:"size-s no-hint",attrs:{type:"text",name:"distributiontime",id:"distributiontime",placeholder:"ss:mm",required:""},domProps:{value:e.rule.dist_time}})])])},a=[],u=(r("8e6e"),r("ac6a"),r("456d"),r("bd86")),c=r("2f62");function l(e,t){var r=Object.keys(e);if(Object.getOwnPropertySymbols){var n=Object.getOwnPropertySymbols(e);t&&(n=n.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),r.push.apply(r,n)}return r}function d(e){for(var t=1;t<arguments.length;t++){var r=null!=arguments[t]?arguments[t]:{};t%2?l(Object(r),!0).forEach((function(t){Object(u["a"])(e,t,r[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(r)):l(Object(r)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(r,t))}))}return e}var p={name:"DistDateTime",computed:d({},Object(c["c"])(["rule"])),mounted:function(){jQuery("#distributiondate").datepicker(),jQuery("#distributiontime").timepicker()}},f=p,m=r("2877"),g=Object(m["a"])(f,i,a,!1,null,"4e1b6db2",null),h=g.exports,b=function(){var e=this,t=e.$createElement,r=e._self._c||t;return r("div",{attrs:{id:"course_capacity"}},[r("h3",[e._v("Schritt 2: Kapazitäten der Veranstaltungen festlegen")]),r("table",{staticClass:"default"},[e._m(0),e._m(1),r("tbody",e._l(e.course_infos,(function(t,n){return r("tr",{key:n},[r("td",[r("a",{attrs:{href:"/dispatch.php/course/details/index/"+t.course_id,"data-dialog":""}},[r("img",{staticClass:"icon-role-inactive icon-shape-info-circle",attrs:{title:"Veranstaltungsdetails aufrufen",src:"/assets/images/icons/grey/info-circle.svg",alt:"Veranstaltungsdetails aufrufen",width:"16",height:"16"}})])]),r("td",[e._v(e._s(t.name))]),r("td",[r("span",{domProps:{innerHTML:e._s(t.times_rooms)}})]),r("td",[r("input",{attrs:{type:"number",min:"0",placeholder:"0",name:"course_capacity["+t.course_id+"]"},domProps:{value:t.capacity},on:{input:function(r){return e.updateCapacity(r,t.course_id)}}})])])})),0)])])},_=[function(){var e=this,t=e.$createElement,r=e._self._c||t;return r("thead",[r("tr",[r("th"),r("th",[e._v("Veranstaltung")]),r("th",[e._v("Zeit/Veranstaltungsort")]),r("th",[r("span",{staticClass:"required"},[e._v("max. Teilnehmendenanzahl")])])])])},function(){var e=this,t=e.$createElement,r=e._self._c||t;return r("colgroup",[r("col",{attrs:{width:"18"}}),r("col"),r("col"),r("col")])}];function v(e,t){var r=Object.keys(e);if(Object.getOwnPropertySymbols){var n=Object.getOwnPropertySymbols(e);t&&(n=n.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),r.push.apply(r,n)}return r}function y(e){for(var t=1;t<arguments.length;t++){var r=null!=arguments[t]?arguments[t]:{};t%2?v(Object(r),!0).forEach((function(t){Object(u["a"])(e,t,r[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(r)):v(Object(r)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(r,t))}))}return e}var O={name:"CourseCapacity",computed:y({},Object(c["c"])(["course_infos","rule"])),methods:{updateCapacity:function(e,t){this.$store.dispatch("updateCapacity",{course_id:t,capacity:e.target.value})}}},j=O,w=Object(m["a"])(j,b,_,!1,null,"ae729bf2",null),P=w.exports,k=function(){var e=this,t=e.$createElement,r=e._self._c||t;return r("div",{attrs:{id:"ranking_groups"}},[r("h3",[e._v("Schritt 3: Zuteilungsgruppen konfigurieren")]),r("section",{staticClass:"contentbox"},[0===Object.keys(e.rule.groups).length?r("div",{staticClass:"messagebox messagebox_info"},[e._v("\n            Keine Zuteilungsgruppe vorhanden.\n        ")]):e._e(),e._l(e.rule.groups,(function(e,t){return r("RankingGroup",{key:t,attrs:{id:t,group:e}})})),r("button",{staticClass:"button",on:{click:function(t){return t.preventDefault(),e.addGroup(t)}}},[e._v("Neue Zuteilungsgruppe erstellen")])],2)])},C=[],I=function(){var e=this,t=e.$createElement,r=e._self._c||t;return r("article",{},[r("header",[r("h1",[r("a",{attrs:{href:"#"}},[e._v("\n                "+e._s(e.group.groupName)+"\n            ")])]),r("nav",[r("a",{attrs:{"data-dialog":"size=auto;"},on:{click:function(t){return t.preventDefault(),e.deleteGroup(t)}}},[r("svg",{attrs:{width:"16",height:"16",xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 16 16","shape-rendering":"geometricPrecision",fill:"#24437c"}},[r("path",{attrs:{d:"M9.759 2.438V1.03H6.24v1.408H2.717v1.917h10.565V2.438H9.759zM3.661 14.97h8.645V5.388H3.661v9.582zm6.163-8.075h1.016v6.567H9.824V6.895zm-2.35 0h1.017v6.567H7.474V6.895zm-2.349 0h1.016v6.567H5.125V6.895z"}})])])])]),r("section",[r("label",{attrs:{for:"name"}},[r("span",{staticClass:"required"},[e._v("Name")]),r("div",{staticClass:"length-hint-wrapper",staticStyle:{width:"667px"}},[e._m(0),r("input",{directives:[{name:"model",rawName:"v-model",value:e.group.groupName,expression:"group.groupName"}],attrs:{type:"text",id:"name",size:"75",maxlength:"255",required:"","aria-required":"true"},domProps:{value:e.group.groupName},on:{input:function(t){t.target.composing||e.$set(e.group,"groupName",t.target.value)}}})])]),r("label",{attrs:{for:"min_amount_prios"}},[r("span",{staticClass:"required"},[e._v("Minimale Anzahl einzureichende Prioritäten")]),r("input",{directives:[{name:"model",rawName:"v-model",value:e.group.minAmountPrios,expression:"group.minAmountPrios"}],attrs:{type:"number",id:"min_amount_prios",min:"0",max:Object.keys(e.group.bundleItems).length},domProps:{value:e.group.minAmountPrios},on:{input:function(t){t.target.composing||e.$set(e.group,"minAmountPrios",t.target.value)}}})]),e._m(1),r("table",{staticClass:"labeled default",attrs:{id:"assigned"}},[e._m(2),e._m(3),r("tbody",[0===Object.keys(e.group.bundleItems).length?r("tr",[r("td"),e._m(4)]):e._e(),e._l(e.sortedBundleItems,(function(t){var n=t[0],o=t[1];return e._l(e.bundleCourseDetails(o.courses),(function(t,s){return r("tr",{key:t.course_id},[0===s?r("td",{style:e.mergedIndicator(o),attrs:{rowspan:Object.keys(o.courses).length}},[r("input",{directives:[{name:"model",rawName:"v-model",value:e.checkboxed[n],expression:"checkboxed[item_id]"}],attrs:{type:"checkbox"},domProps:{checked:Array.isArray(e.checkboxed[n])?e._i(e.checkboxed[n],null)>-1:e.checkboxed[n]},on:{change:function(t){var r=e.checkboxed[n],o=t.target,s=!!o.checked;if(Array.isArray(r)){var i=null,a=e._i(r,i);o.checked?a<0&&e.$set(e.checkboxed,n,r.concat([i])):a>-1&&e.$set(e.checkboxed,n,r.slice(0,a).concat(r.slice(a+1)))}else e.$set(e.checkboxed,n,s)}}})]):e._e(),r("td",[e._v(e._s(t.name))]),r("td",[r("span",{domProps:{innerHTML:e._s(t.times_rooms)}})]),r("td",[e._v(e._s(t.capacity))])])}))}))],2),r("tfoot",[r("tr",[r("td",{attrs:{colspan:"4"}},[r("select",{directives:[{name:"model",rawName:"v-model",value:e.action,expression:"action"}],staticClass:"select-action",attrs:{disabled:0===e.selectedBundleItems.length},on:{change:function(t){var r=Array.prototype.filter.call(t.target.options,(function(e){return e.selected})).map((function(e){var t="_value"in e?e._value:e.value;return t}));e.action=t.target.multiple?r:r[0]}}},[r("option",{attrs:{selected:"",disabled:"",value:""}},[e._v("Aktionen")]),e.selectedBundleItems.length>0?r("option",{attrs:{value:"delete"}},[e._v("Zuordnung löschen")]):e._e(),e.selectedBundleItems.length>1?r("option",{attrs:{value:"merge"}},[e._v("Zusammenfassen")]):e._e(),e.splittableBundleItem?r("option",{attrs:{value:"split"}},[e._v("Trennen")]):e._e()]),r("button",{staticClass:"button",on:{click:function(t){return t.preventDefault(),e.delegateAction(t)}}},[e._v("Ausführen")])])])])]),r("label",{attrs:{for:"non_assigned"}},[e._v("\n            Keiner Zuteilungsgruppe zugeordnet\n        ")]),r("table",{staticClass:"labeled default",attrs:{id:"non_assigned"}},[e._m(5),e._m(6),r("tbody",[0===e.notAssignedCourseDetails.length?r("tr",[r("td"),e._m(7)]):e._e(),e._l(e.notAssignedCourseDetails,(function(t){return r("tr",{key:t.course_id},[r("td",[r("button",{staticClass:"add-button",on:{click:function(r){return r.preventDefault(),e.addCourseToGroup(t.course_id)}}},[r("svg",{attrs:{width:"16",height:"16",xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 16 16","shape-rendering":"geometricPrecision",fill:"#24437c"}},[r("path",{attrs:{d:"M8 1.001a7 7 0 1 0 .003 14 7 7 0 0 0-.003-14zm4.016 8.024H9.023l.002 2.943-2.048.001.001-2.943H3.984l-.001-2.05h2.994V4l2.047-.001v2.976l2.993.001-.001 2.049z"}})])])]),r("td",[e._v(e._s(t.name))]),r("td",[r("span",{domProps:{innerHTML:e._s(t.times_rooms)}})]),r("td",[e._v(e._s(t.capacity))])])}))],2)])]),r("input",{attrs:{type:"hidden",name:"groups["+e.id+"]"},domProps:{value:JSON.stringify(e.group)}})])},x=[function(){var e=this,t=e.$createElement,r=e._self._c||t;return r("div",{staticClass:"length-hint",staticStyle:{display:"none"}},[e._v("\n                    Zeichen verbleibend: "),r("span",{staticClass:"length-hint-counter"},[e._v("255")])])},function(){var e=this,t=e.$createElement,r=e._self._c||t;return r("label",{attrs:{for:"assigned"}},[r("span",{staticClass:"required"},[e._v("Zugeordnete Veranstaltungen")])])},function(){var e=this,t=e.$createElement,r=e._self._c||t;return r("thead",[r("tr",[r("th"),r("th",[e._v("Veranstaltung")]),r("th",[e._v("Zeit/Veranstaltungsort")]),r("th",[e._v("max. Teilnehmendenanzahl")])])])},function(){var e=this,t=e.$createElement,r=e._self._c||t;return r("colgroup",[r("col",{attrs:{width:"35"}}),r("col"),r("col"),r("col")])},function(){var e=this,t=e.$createElement,r=e._self._c||t;return r("td",{attrs:{colspan:"3"}},[r("em",[e._v("keine zugeordneten Veranstaltungen")])])},function(){var e=this,t=e.$createElement,r=e._self._c||t;return r("thead",[r("tr",[r("th"),r("th",[e._v("Veranstaltung")]),r("th",[e._v("Zeit/Veranstaltungsort")]),r("th",[e._v("max. Teilnehmendenanzahl")])])])},function(){var e=this,t=e.$createElement,r=e._self._c||t;return r("colgroup",[r("col",{attrs:{width:"35"}}),r("col"),r("col"),r("col")])},function(){var e=this,t=e.$createElement,r=e._self._c||t;return r("td",{attrs:{colspan:"3"}},[r("em",[e._v("keine zuzuordnenden Veranstaltungen")])])}],D=r("768b");r("ffc1"),r("55dd"),r("7f7f");function B(e,t){var r=Object.keys(e);if(Object.getOwnPropertySymbols){var n=Object.getOwnPropertySymbols(e);t&&(n=n.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),r.push.apply(r,n)}return r}function E(e){for(var t=1;t<arguments.length;t++){var r=null!=arguments[t]?arguments[t]:{};t%2?B(Object(r),!0).forEach((function(t){Object(u["a"])(e,t,r[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(r)):B(Object(r)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(r,t))}))}return e}var S={name:"RankingGroup",data:function(){return{checkboxed:{},action:""}},props:["id","group"],computed:E({},Object(c["c"])(["course_infos"]),{},Object(c["b"])(["notAssignedCourseDetails"]),{selectedBundleItems:function(){var e=this;return Object.keys(this.checkboxed).filter((function(t){return!0===e.checkboxed[t]}))},splittableBundleItem:function(){return 1===this.selectedBundleItems.length&&Object.keys(this.group.bundleItems[this.selectedBundleItems[0]].courses).length>1},sortedBundleItems:function(){var e=E({},this.group.bundleItems),t=!1;for(var r in e)void 0!==this.course_infos[Object.keys(e[r].courses)[0]]?(e[r].sortingName=this.course_infos[Object.keys(e[r].courses)[0]].name,t=!0):delete e[r];return t?Object.entries(e).sort((function(e,t){var r=Object(D["a"])(e,2),n=r[1],o=Object(D["a"])(t,2),s=o[1];return n.sortingName.localeCompare(s.sortingName)})):[]}}),methods:{mergedIndicator:function(e){return Object.keys(e.courses).length>1?{borderRight:"3px solid #e7ebf1"}:{}},bundleCourseDetails:function(e){var t=[];for(var r in e)e.hasOwnProperty(r)&&this.course_infos.hasOwnProperty(r)&&t.push(E({},this.course_infos[r]));return t.sort((function(e,t){return e.name.localeCompare(t.name)})),t},deleteGroup:function(){this.$store.dispatch("deleteGroup",{group_id:this.id})},addCourseToGroup:function(e){this.$store.dispatch("addBundleItem",{group_id:this.id,course_ids:[e]})},delegateAction:function(){"delete"===this.action?this.$store.dispatch("deleteBundleItems",{group_id:this.id,item_ids:this.selectedBundleItems}):"merge"===this.action?this.$store.dispatch("mergeBundleItems",{group_id:this.id,item_ids:this.selectedBundleItems}):"split"===this.action&&this.$store.dispatch("splitBundleItem",{group_id:this.id,item_id:this.selectedBundleItems[0]}),this.action="",this.checkboxed={}}}},$=S,A=(r("aff9"),Object(m["a"])($,I,x,!1,null,"47eae3ca",null)),z=A.exports;function G(e,t){var r=Object.keys(e);if(Object.getOwnPropertySymbols){var n=Object.getOwnPropertySymbols(e);t&&(n=n.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),r.push.apply(r,n)}return r}function V(e){for(var t=1;t<arguments.length;t++){var r=null!=arguments[t]?arguments[t]:{};t%2?G(Object(r),!0).forEach((function(t){Object(u["a"])(e,t,r[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(r)):G(Object(r)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(r,t))}))}return e}var N={name:"RankingGroupList",components:{RankingGroup:z},computed:V({},Object(c["c"])(["rule"])),methods:{addGroup:function(){this.$store.dispatch("addGroup")}}},T=N,H=(r("30f3"),Object(m["a"])(T,k,C,!1,null,"0f8eb996",null)),Z=H.exports,q={name:"configure_rule",components:{RankingGroupList:Z,CourseCapacity:P,DistDateTime:h},created:function(){var e=STUDIP.Dialog.getInstance("configurerule");void 0!==e&&jQuery(e.element).dialog("option","width",window.screen.width/3*2),this.$store.dispatch("initialState")}},M=q,L=Object(m["a"])(M,o,s,!1,null,null,null),R=L.exports,J=r("7618"),K=(r("6762"),r("2fdb"),r("75fc")),Q=r("bc3a"),U=r.n(Q);function F(e,t){var r=Object.keys(e);if(Object.getOwnPropertySymbols){var n=Object.getOwnPropertySymbols(e);t&&(n=n.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),r.push.apply(r,n)}return r}function W(e){for(var t=1;t<arguments.length;t++){var r=null!=arguments[t]?arguments[t]:{};t%2?F(Object(r),!0).forEach((function(t){Object(u["a"])(e,t,r[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(r)):F(Object(r)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(r,t))}))}return e}n["a"].use(c["a"]);var X=new c["a"].Store({state:{rule:{},course_infos:{},selected_course_ids:[]},getters:{assignedCourses:function(e){var t=[];for(var r in e.rule.groups)if(e.rule.groups.hasOwnProperty(r)){var n=e.rule.groups[r];for(var o in n.bundleItems)n.bundleItems.hasOwnProperty(o)&&t.push.apply(t,Object(K["a"])(Object.keys(n.bundleItems[o].courses)))}return t},notAssignedCourses:function(e,t){return e.selected_course_ids.filter((function(e){return!t.assignedCourses.includes(e)}))},notAssignedCourseDetails:function(e,t){var r=[];return t.notAssignedCourses.forEach((function(t){void 0!==e.course_infos[t]&&r.push(W({},e.course_infos[t]))})),r.sort((function(e,t){return e.name.localeCompare(t.name)})),r}},mutations:{setRule:function(e,t){e.rule=t},setSelectedCourseIds:function(e,t){e.selected_course_ids=t},setCourseInfos:function(e,t){e.course_infos=t},setCourseInfoById:function(e,t){var r=t.course_id,n=t.info;e.course_infos[r]=n},addGroup:function(e,t){n["a"].set(e.rule.groups,t.id,t)},deleteGroup:function(e,t){var r=t.group_id;n["a"].delete(e.rule.groups,r)},addBundleItem:function(e,t){var r=t.group_id,o=t.item;n["a"].set(e.rule.groups[r].bundleItems,o.id,o)},deleteBundleItem:function(e,t){var r=t.group_id,o=t.item_id;n["a"].delete(e.rule.groups[r].bundleItems,o)}},actions:{initialState:function(e){var t=e.commit,r=JSON.parse(BUNDLEALLOCATION_DATA),n=Object(K["a"])(document.querySelectorAll('#instcourses input[name="courses[]"]:checked')).map((function(e){return e.value}));t("setRule",r),t("setSelectedCourseIds",n),U.a.post("/plugins.php/bundleallocationplugin/rule/course_infos",{course_ids:n}).then((function(e){for(var n in e.data)!0===e.data.hasOwnProperty(n)&&null!==r.course_capacity&&void 0!==Object(J["a"])(r.course_capacity[n])&&(e.data[n].capacity=r.course_capacity[n]);t("setCourseInfos",e.data)})).catch((function(e){return console.error(e)}))},updateCapacity:function(e,t){var r=e.commit,n=e.state,o=t.course_id,s=t.capacity,i=n.course_infos[o];i.capacity=s,r("setCourseInfoById",{course_id:o,info:i})},addGroup:function(e){var t=e.commit,r=e.state,n=new FormData;n.append("rule_id",r.rule.rule_id),n.append("group_name","Neue Zuteilungsgruppe"),U.a.post("/plugins.php/bundleallocationplugin/rule/add_ranking_group",n).then((function(e){t("addGroup",e.data)})).catch((function(e){return console.error(e)}))},deleteGroup:function(e,t){var r=e.commit,n=t.group_id;r("deleteGroup",{group_id:n})},addBundleItem:function(e,t){var r=e.commit,n=t.group_id,o=t.course_ids,s=new FormData;s.append("group_id",n),U.a.post("/plugins.php/bundleallocationplugin/rule/add_bundle_item",s).then((function(e){o.forEach((function(t){e.data.courses[t]=!0})),r("addBundleItem",{group_id:n,item:e.data})}))},deleteBundleItems:function(e,t){var r=e.commit,n=t.group_id,o=t.item_ids;o.forEach((function(e){r("deleteBundleItem",{group_id:n,item_id:e})}))},mergeBundleItems:function(e,t){var r=e.dispatch,n=e.state,o=t.group_id,s=t.item_ids,i=[];s.forEach((function(e){i.push.apply(i,Object(K["a"])(Object.keys(n.rule.groups[o].bundleItems[e].courses)))})),r("addBundleItem",{group_id:o,course_ids:i}).then((function(){r("deleteBundleItems",{group_id:o,item_ids:s})}))},splitBundleItem:function(e,t){var r=e.dispatch,n=e.state,o=t.group_id,s=t.item_id;Object.keys(n.rule.groups[o].bundleItems[s].courses).forEach((function(e){r("addBundleItem",{group_id:o,course_ids:[e]})})),r("deleteBundleItems",{group_id:o,item_ids:[s]})}}});n["a"].config.productionTip=!1,new n["a"]({store:X,render:function(e){return e(R)}}).$mount("#configure_rule")},aff9:function(e,t,r){"use strict";var n=r("d592"),o=r.n(n);o.a},d592:function(e,t,r){}});
//# sourceMappingURL=configure_rule.eac82d17.js.map
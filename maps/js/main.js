define("appstate",[],function(){var e=new can.Map({new_feature:null,delete_feature:null,edit_feature:null,selected_folder:"root",selected_object:null,admin_mode:!1,admin_page:"mapeditor",username:"",clicked_feature:null,sidebar_panel:"sidebar_panel_main",message_error:null,message_ok:null,message_info:null,selected_iconset:null,title:null,menu_toggle:!0,search_phrase:"",zoomto_latlng:null,find_route:null});return e}),define("maputil",[],function(){var e={};return e.parsePos=function(e){if(!e)return!1;var t=$.trim(e).match(/^\(?([-+]?\d{1,2}[.]\d+),\s*([-+]?\d{1,3}[.]\d+)\)?$/);return t?{lat:parseFloat(t[1]),lng:parseFloat(t[2])}:!1},e.parsePath=function(t){if(!t)return[];var n=t.split("|"),r=[];for(var i=0;i<n.length;i++)r.push(e.parsePos(n[i]));return r},e.generateID=function(e){var t=(new Date).getTime(),n="xxxxxxxxyy".replace(/[xy]/g,function(e){var n=(t+Math.random()*16)%16|0;return t=Math.floor(t/16),(e=="x"?n:n&7|8).toString(16)});return(typeof e!="undefined"?e:"")+n},e.translate=function(e,t){return typeof maptranslations=="undefined"||typeof maptranslations[e]=="undefined"||maptranslations[e]===""?e:maptranslations[e]},e.translatep=function(e,t){return e},e.getMapUrl=function(){return(window.location.protocol+"//"+window.location.hostname+window.location.pathname.replace("/admin.php","/index.php")).toLowerCase()},e.getMapUrlPath=function(){return e.getMapUrl().replace("/index.php","/")},e.benc=function(e){var t="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",n,r,i,s,o,u,a,f,l=0,c=0,h="",p=[];if(!e)return e;do n=e.charCodeAt(l++),r=e.charCodeAt(l++),i=e.charCodeAt(l++),f=n<<16|r<<8|i,s=f>>18&63,o=f>>12&63,u=f>>6&63,a=f&63,p[c++]=t.charAt(s)+t.charAt(o)+t.charAt(u)+t.charAt(a);while(l<e.length);h=p.join("");var d=e.length%3;return(d?h.slice(0,d-3):h)+"===".slice(d||3)},e.bdec=function(e){var t="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",n,r,i,s,o,u,a,f,l=0,c=0,h="",p=[];if(!e)return e;e+="";do s=t.indexOf(e.charAt(l++)),o=t.indexOf(e.charAt(l++)),u=t.indexOf(e.charAt(l++)),a=t.indexOf(e.charAt(l++)),f=s<<18|o<<12|u<<6|a,n=f>>16&255,r=f>>8&255,i=f&255,u==64?p[c++]=String.fromCharCode(n):a==64?p[c++]=String.fromCharCode(n,r):p[c++]=String.fromCharCode(n,r,i);while(l<e.length);return h=p.join(""),h.replace(/\0+$/,"")},e}),define("mapdata",["appstate","maputil"],function(e,t){var n={};n.Feature=can.Model.extend({create:"POST ajax_admin.php?action=create",update:"POST ajax_admin.php?action=update",destroy:"POST ajax_admin.php?action=destroy&id={id}",findAll:"GET ajax.php?action=get",findOne:"GET ajax.php?action=get&id={id}",init:function(){this.validate("name",function(e){if(_.isUndefined(e)||!e)return t.translate("Name of object is required")})}},{parent:"root",sort:100}),n.MapSettings=can.Model.extend({findOne:"GET ajax.php?action=mapsettings",update:"POST ajax_admin.php?action=mapsettings_update"},{center:"45.089035564831036,-33.398439499999995",zoom:2,maptype:"roadmap",apikey:null,menu_icon_maxheight:null,menu_icon_maxwidth:null,enable_search:!0,enable_clusterer:!1,enable_route:!0}),n.Folder=n.Feature.extend({findAll:"GET ajax.php?action=gettree",init:function(){this._super()}},{default_display:!0,menu_icon_url:"mapicons/folder.png"}),n.Marker=n.Feature.extend({init:function(){this._super(),this.validate("position",function(e){if(_.isUndefined(e)||!e)return t.translate("Position is required")})}},{icon_url:"mapicons/red-dot.png",menu_icon_url:"mapicons/red-dot.png"}),n.Polyline=n.Feature.extend({init:function(){this._super(),this.validate("path",function(e){if(_.isUndefined(e)||!e)return t.translate("Vertexes are required")})}},{strokeColor:"#FF0000",strokeOpacity:1,strokeWeight:2,menu_icon_url:"mapicons/pico_path.png"}),n.Polygon=n.Feature.extend({init:function(){this._super(),this.validate("path",function(e){if(_.isUndefined(e)||!e)return t.translate("Vertexes are required")})}},{strokeColor:"#FF0000",strokeOpacity:1,strokeWeight:2,fillColor:"#FF0000",fillOpacity:.3,menu_icon_url:"mapicons/pico_area.png"});var r=can.Map.extend({},{exists:!0,edit_mode:!1,display:!0}),i={};i.Feature=can.Construct.extend({type:"Feature",data:null,state:null,editable:!1,container:!1,enable_infowindow:!1,bounds:null,overlay_obj:null,init:function(t){_.isUndefined(t)&&(t={});if(t.data)var i=t.data.attr();else var i={};this.data=new n[this.type](i),this.data.attr("type")||this.data.attr("type",this.type),this.data.attr("parent")||this.data.attr("parent","root"),this.state=t.state||new r,this.container&&this.state.attr("checkable",!0),e.attr("admin_mode")&&(this.editable=!0),this.data.bind("destroyed",this.proxy(function(){this.state.attr("exists",!1),this.overlay_obj=null}))}}),i.Folder=i.Feature.extend({type:"Folder",container:!0,enable_infowindow:!1,init:function(e){this._super(e),this.data.attr("default_display")?(this.state.attr("display",!0),this.state.attr("content_loaded",!0)):(this.state.attr("display",!1),this.state.attr("content_loaded",!1))}}),i.MapSettings=i.Feature.extend({type:"MapSettings"}),i.Marker=i.Feature.extend({type:"Marker",enable_infowindow:!0}),i.Polyline=i.Feature.extend({type:"Polyline",enable_infowindow:!1}),i.Polygon=i.Polyline.extend({type:"Polygon",enable_infowindow:!1});var s=can.Construct.extend({features:[],add:function(e,t){if(this.getFeatureById(e.data.attr("id")))return;this.features.push(e),this._bindFeature(e),(_.isUndefined(t)||!t)&&can.trigger(this,"add",e)},remove:function(e,t){var n=this.features.indexOf(e);n>-1&&this.features.splice(n,1),(_.isUndefined(t)||!t)&&can.trigger(this,"remove",e)},loadFeatures:function(e){if(_.isUndefined(e)||!e)return;var t=new can.Deferred,r=this.getFeatureById(e);t.done(this.proxy(function(){r&&r.state.attr("content_loaded",!0);for(var t=0;t<this.features.length;t++){if(this.features[t].data.attr("parent")!=e)continue;(this.features[t].container&&this.features[t].data.attr("default_display")||!this.features[t].container)&&this.features[t].state.attr("display",!0)}}));if(r&&r.state.attr("content_loaded"))t.resolve();else if(typeof window.bootstrap_data!="undefined"&&typeof window.bootstrap_data[e]!="undefined"&&_.isArray(window.bootstrap_data[e])){for(var i=0;i<window.bootstrap_data[e].length;i++){var s=new n.Feature(window.bootstrap_data[e][i]);this._makeFeatureFromModel(s)}t.resolve()}else n.Feature.findAll({parent:e},this.proxy(function(e){e&&e.forEach(this._makeFeatureFromModel,this),t.resolve()}),this.proxy(function(e){t.reject(e)}));return t},_makeFeatureFromModel:function(e){var t=e.attr("type");if(!t||_.isUndefined(i[t]))return;var n=new i[t]({data:e});this.add(n)},_bindFeature:function(e){e.state.bind("display",this.proxy(function(t,n,r){if(e.container)if(n)this.loadFeatures(e.data.attr("id"));else for(var i=0;i<this.features.length;i++){if(this.features[i].data.attr("parent")!=e.data.attr("id"))continue;this.features[i].state.attr("display",!1)}}))},removeById:function(e){var t=this.getFeatureById(e);this.remove(t)},getLength:function(){return this.features.length},getFeature:function(e){return this.features[e]},getFeatureById:function(e){if(!e)return null;var t=this.features.length;for(var n=0;n<t;n++)if(this.features[n].data.attr("id")==e)return this.features[n]},getFeaturesByParent:function(e){var t=[];if(!e)return t;var n=this.features.length;for(var r=0;r<n;r++)this.features[r].data.attr("parent")==e&&t.push(this.features[r]);return t},areUncheckedFolders:function(){var e=this.features.length;for(var t=0;t<e;t++)if(this.features[t].type=="Folder"&&!this.features[t].state.attr("display"))return!0;return!1}});return{StateModel:r,FeatureModel:n.Feature,FolderModel:n.Folder,MarkerModel:n.Marker,PolylieModel:n.Polylie,PolygonModel:n.Polygon,MapSettingsModel:n.MapSettings,Feature:i.Feature,MapSettings:i.MapSettings,Folder:i.Folder,Marker:i.Marker,Polyline:i.Polyline,Polygon:i.Polygon,MapData:s}}),define("mapcontrols",["appstate","maputil"],function(e,t){var n=can.Control.extend({init:function(e,t){this.element.html(can.view("../tpl/menuel.mustache",this.options.feature))},clickedEl:function(){var t=this.options.feature.data.attr("id");e.attr("selected_object",t);if(!this.options.feature.container){e.attr("clicked_feature",t);var n=this.options.feature.data.attr("parent");e.attr("selected_folder",n)}else this.options.feature.state.attr("display")&&this.element.find(".menuel-content:first").toggle("fast"),e.attr("selected_folder",t);e.attr("admin_mode")&&($(".menuel-selected").removeClass("menuel-selected"),this.element.find(".menuel-title-container:first").addClass("menuel-selected"))},"{feature.state} exists":function(e,t,n,r){r&&!n&&(this.element.empty(),this.destroy())},".menuel-name:first click":function(e,t){t.preventDefault(),this.clickedEl()},"img.menuel-icon:first click":function(e,t){t.preventDefault(),this.clickedEl()},".menuel-checkbox:first click":function(e,t){if(!this.options.feature.container)return;e.is(":checked")?this.element.find(".menuel-content:first").show("fast"):this.element.find(".menuel-content:first").hide("fast")}}),r=can.Control.extend({init:function(e,t){this.options.mapdata.getLength()&&this.buildTree()},buildTree:function(){this.overflow_protect=[],this._buildTree(this.element,"root",0),this.overflow_protect=null},_buildTree:function(e,t,r){this.overflow_protect.push(t);var i=this.options.mapdata.getLength();for(var s=0;s<i;s++){var o=this.options.mapdata.getFeature(s);if(!o||o.data.attr("parent")!=t)continue;var u=o.data.attr("id"),a=$("<div>");$(a).addClass("menuel");var f=new n(a,{feature:o});e.append(f.element),o.state.attr("in_menu",!0),u&&($(a).attr("id",u),$.inArray(u,this.overflow_protect)==-1&&this._buildTree(f.element.find(".menuel-content"),u,r+1))}},addMenuElement:function(e){if(e.state.attr("in_menu")||e.state.attr("no_menu"))return;var t=e.data.attr("parent"),r=e.data.attr("id");if(!t)return;var i=$("<div>",{"class":"menuel",id:r}),s=new n(i,{feature:e});e.state.attr("in_menu",!0),t=="root"?this.element.append(s.element):this.element.find("#"+t+" > .menuel-content").append(s.element)},"{mapdata} add":function(e,t,n){this.addMenuElement(n)}}),i=can.Control.extend({init:function(e,n){this.element.html(can.view("../tpl/infocontent.mustache",{feature:this.options.feature,enable_route:n.enable_route,enable_tools:this.options.feature.type=="Marker"?!0:!1,gps:this.options.feature.data.attr("position")?t.parsePos(this.options.feature.data.attr("position")):null}))},".infocontent-img-anchor click":function(e,t){t.preventDefault(),e.ekkoLightbox()},"#infocontent-tools-gps click":function(e,t){t.preventDefault(),can.trigger(this,"infocontent_resize"),this.element.find(".infocontent-tools-add").not("#infocontent-tools-gps-add").hide(),this.element.find("#infocontent-tools-gps-add").toggle()},"#infocontent-tools-route click":function(e,t){t.preventDefault(),can.trigger(this,"infocontent_resize"),this.element.find(".infocontent-tools-add").not("#infocontent-tools-route-add").hide(),this.element.find("#infocontent-tools-route-add").is(":visible")?this.element.find("#infocontent-tools-route-add").hide():(this.element.find("#infocontent-tools-route-add").show(),this.element.find("input[type=text]:first").focus())},"#infocontent-tools-zoomin click":function(t,n){n.preventDefault(),this.options.feature.data.attr("position")&&e.attr("zoomto_latlng",this.options.feature.data.attr("position"))},"#route_form submit":function(t,n){n.preventDefault();var r=this.element.find("#route_form_from");if(!r.val())r.parent().addClass("has-error");else{r.parent().removeClass("has-error");var i={from:r.val(),to_position:this.options.feature.data.attr("position")};e.attr("find_route",i),r.val("")}}});return{Infocontent:i,MapTree:r,MenuElement:n}}),define("gm_overlay",["appstate","maputil"],function(e,t){var n=can.Construct.extend({feature:null,overlay:null,map:null,_feature_bindings:{},_overlay_bindings:{},init:function(e){this.feature=e.feature,this.map=e.map},makeOverlay:function(e){this.feature.state.attr("overlay_created",!0),this.feature.overlay_obj=this.overlay}}),r=n.extend({init:function(e){this._super(e);var t=this.prepareOptions();this.makeOverlay(t),this.overlay.setMap(this.map),this.map.setCenter(t.position),this.map.setZoom(parseInt(this.feature.data.attr("zoom"))),this.map.setMapTypeId(this.feature.data.attr("maptype")),this.bindOverlay(),this.bindFeature()},prepareOptions:function(){var e={};e.title=t.translate("Map center point");var n=t.parsePos(this.feature.data.attr("center"));return n&&(e.position=new google.maps.LatLng(n.lat,n.lng)),e.icon={anchor:new google.maps.Point(21,18),url:"lay/center.png"},e},makeOverlay:function(e){this.overlay=new google.maps.Marker(e),this._super()},bindOverlay:function(){this._overlay_bindings.drag=google.maps.event.addListener(this.map,"drag",this.proxy(function(){var e=this.map.getCenter();this.overlay.setPosition(e),this.feature.data.attr("center",e.lat()+","+e.lng())})),this._overlay_bindings.center_changed=google.maps.event.addListener(this.map,"center_changed",this.proxy(function(){var e=this.map.getCenter();this.overlay.setPosition(e),this.feature.data.attr("center",e.lat()+","+e.lng())})),this._overlay_bindings.zoom_changed=google.maps.event.addListener(this.map,"zoom_changed",this.proxy(function(){var e=this.map.getZoom();this.feature.data.attr("zoom",e)})),this._overlay_bindings.maptypeid_changed=google.maps.event.addListener(this.map,"maptypeid_changed",this.proxy(function(){var e=this.map.getMapTypeId();this.feature.data.attr("maptype",e)}))},bindFeature:function(){this._feature_bindings.edit_mode=function(e,t,n){if(!t){this.overlay.setMap(null);for(var r in this._overlay_bindings)google.maps.event.removeListener(this._overlay_bindings[r]);this.feature.overlay_obj=null,this.feature.state.attr("overlay_created",!1)}},this.feature.state.bind("edit_mode",this.proxy(this._feature_bindings.edit_mode))}}),i=n.extend({clusterer:null,init:function(e){this._super(e);var t=this.prepareOptions();this.makeOverlay(t),this.feature.state.attr("display")&&this.markerOn(),this.bindFeature(),this.bindOverlay()},markerOn:function(){this.overlay.setMap(this.map)},markerOff:function(){this.overlay.setMap(null)},prepareOptions:function(){var e={};e.title=this.feature.data.attr("name");var n=t.parsePos(this.feature.data.attr("position"));return n&&(e.position=new google.maps.LatLng(n.lat,n.lng)),this.feature.data.attr("icon_url")&&(e.icon=this.feature.data.attr("icon_url")),e},makeOverlay:function(e){this.overlay=new google.maps.Marker(e),this._super()},bindFeature:function(){this._feature_bindings.position=function(e,n,r){var i=t.parsePos(n);if(!i)return;var s=new google.maps.LatLng(i.lat,i.lng);this.overlay.setPosition(i)},this._feature_bindings.name=function(e,t,n){this.overlay.setTitle(t)},this._feature_bindings.icon_url=function(e,t,n){this.overlay.setIcon(t)},this._feature_bindings.display=function(e,t,n){t?this.markerOn():this.markerOff()},this._feature_bindings.exists=function(e,t,n){n&&!t&&this.markerOff()},this._feature_bindings.edit_mode=function(e,t,n){t?(this.overlay.setDraggable(!0),this.feature.data.isNew()&&google.maps.event.addListenerOnce(this.map,"click",this.proxy(function(e){this.overlay.setPosition(e.latLng),this.overlay.setMap(this.map)}))):this.overlay.setDraggable(!1)},this.feature.data.bind("position",this.proxy(this._feature_bindings.position)),this.feature.data.bind("name",this.proxy(this._feature_bindings.name)),this.feature.data.bind("icon_url",this.proxy(this._feature_bindings.icon_url)),this.feature.state.bind("edit_mode",this.proxy(this._feature_bindings.edit_mode)),this.feature.state.bind("display",this.proxy(this._feature_bindings.display)),this.feature.state.bind("exists",this.proxy(this._feature_bindings.exists))},bindOverlay:function(){this._overlay_bindings.position=google.maps.event.addListener(this.overlay,"position_changed",this.proxy(function(){var e=this.overlay.getPosition();this.feature.data.attr("position",e.lat()+","+e.lng())})),this._overlay_bindings.click=google.maps.event.addListener(this.overlay,"click",this.proxy(function(){var t=this.feature.data.attr("id");e.attr("clicked_feature",t)}))}}),s=n.extend({init:function(e){this._super(e);var t=this.prepareOptions();this.makeOverlay(t),this.feature.state.attr("display")&&this.overlay.setMap(this.map),this.bindFeature(),this.bindOverlay()},makeOverlay:function(e){this.overlay=new google.maps.Polyline(e),this.setBounds(e.path),this._super()},prepareOptions:function(){var e={};return e.path=t.parsePath(this.feature.data.attr("path")),e.strokeColor=this.feature.data.attr("strokeColor"),e.strokeOpacity=parseFloat(this.feature.data.attr("strokeOpacity")),e.strokeWeight=parseInt(this.feature.data.attr("strokeWeight")),e},setBounds:function(e){var t=new google.maps.LatLngBounds;if(e instanceof google.maps.MVCArray)e.forEach(function(e){t.extend(e)});else if(_.isArray(e))for(var n=0;n<e.length;n++)t.extend(new google.maps.LatLng(e[n].lat,e[n].lng));this.feature.bounds=t},bindFeature:function(){this._feature_bindings.path=function(e,n,r){var i=t.parsePath(n);if(!i)return;this.overlay.setPath(i),this.setBounds(i)},this._feature_bindings.strokeColor=function(e,t,n){if(!t)return;this.overlay.setOptions({strokeColor:t})},this._feature_bindings.strokeOpacity=function(e,t,n){if(!t)return;var r=parseFloat(t);r<0&&(r=0),r>1&&(r=1),this.overlay.setOptions({strokeOpacity:r})},this._feature_bindings.strokeWeight=function(e,t,n){var r=parseInt(t);r<0&&(r=0),this.overlay.setOptions({strokeWeight:r})},this._feature_bindings.display=function(e,t,n){t?this.overlay.setMap(this.map):this.overlay.setMap(null)},this._feature_bindings.exists=function(e,t,n){n&&!t&&this.overlay.setMap(null)},this._feature_bindings.edit_mode=function(e,t,n){t?this.startEditMode():this.endEditMode()},this.feature.data.bind("path",this.proxy(this._feature_bindings.path)),this.feature.data.bind("strokeColor",this.proxy(this._feature_bindings.strokeColor)),this.feature.data.bind("strokeWeight",this.proxy(this._feature_bindings.strokeWeight)),this.feature.data.bind("strokeOpacity",this.proxy(this._feature_bindings.strokeOpacity)),this.feature.state.bind("edit_mode",this.proxy(this._feature_bindings.edit_mode)),this.feature.state.bind("display",this.proxy(this._feature_bindings.display)),this.feature.state.bind("exists",this.proxy(this._feature_bindings.exists))},startEditMode:function(){this.overlay.setEditable(!0),this.map.setOptions({draggableCursor:"crosshair"}),this._overlay_bindings.mapclick=google.maps.event.addListener(this.map,"click",this.proxy(function(e){this.overlay.getPath().push(e.latLng)})),this._overlay_bindings.vertexclick=google.maps.event.addListener(this.overlay,"click",this.proxy(function(e){if(typeof e.vertex=="undefined")return;this.overlay.getPath().removeAt(e.vertex)}))},endEditMode:function(){google.maps.event.removeListener(this._overlay_bindings.mapclick),google.maps.event.removeListener(this._overlay_bindings.vertexclick),this.overlay.setEditable(!1),this.map.setOptions({draggableCursor:null});var e=[];this.overlay.getPath().forEach(function(t){e.push(t.lat()+","+t.lng())}),this.feature.data.attr("path",e.join("|"))},bindOverlay:function(){}}),o=s.extend({makeOverlay:function(e){this.overlay=new google.maps.Polygon(e),this.feature.state.attr("overlay_created",!0),this.feature.overlay_obj=this.overlay,this.setBounds(e.path)},prepareOptions:function(){var e=this._super();return e.fillColor=this.feature.data.attr("fillColor"),e.fillOpacity=parseFloat(this.feature.data.attr("fillOpacity")),e},bindFeature:function(){this._super(),this._feature_bindings.fillColor=function(e,t,n){if(!t)return;this.overlay.setOptions({fillColor:t})},this._feature_bindings.fillOpacity=function(e,t,n){if(!t)return;var r=parseFloat(t);r<0&&(r=0),r>1&&(r=1),this.overlay.setOptions({fillOpacity:r})},this.feature.data.bind("fillColor",this.proxy(this._feature_bindings.fillColor)),this.feature.data.bind("fillOpacity",this.proxy(this._feature_bindings.fillOpacity))}});return{Overlay:n,Marker:i,MapSettings:r,Polyline:s,Polygon:o}}),define("gm_infowindow",["appstate","maputil","mapcontrols"],function(e,t,n){var r=can.Construct.extend({overlay:null,map:null,infocontent:null,feature:null,enable_route:!1,init:function(e){this.map=e.map,this.overlay=new google.maps.InfoWindow({maxWidth:450}),this.bindOverlay()},open:function(e){this.removeInfocontent(),this.infocontent=new n.Infocontent($("<div>"),{feature:e,enable_route:this.enable_route});var r=t.parsePos(e.data.attr("position"));if(!r)return;this.feature=e,this.overlay.setPosition(new google.maps.LatLng(r.lat,r.lng)),this.overlay.open(this.map,e.overlay_obj),this.overlay.setContent(this.infocontent.element[0]),e.data.bind("change",this.proxy(this.bindFeature)),can.bind.call(this.infocontent,"infocontent_resize",this.proxy(this.resize))},resize:function(){this.overlay.setContent(this.infocontent.element[0])},close:function(){this.feature&&this.feature.data.unbind("change",this.bindFeature),this.removeInfocontent(),this.overlay.close()},bindFeature:function(e,t,n,r,i){if(t!="name"&&t!="description")return;this.overlay.setContent(this.infocontent.element[0])},removeInfocontent:function(){this.infocontent&&(this.infocontent.element.remove(),this.infocontent.destroy(),this.infocontent=null)},bindOverlay:function(){google.maps.event.addListener(this.overlay,"closeclick",this.proxy(function(){e.attr("clicked_feature",null)}))}});return r}),define("gm_mapctrl",["appstate","gm_overlay","gm_infowindow","maputil"],function(e,t,n,r){var i=can.Control.extend({map:null,enable_infowindow:!0,enable_route:!1,infowindow_ctrl:null,init:function(e,t){var i=r.parsePos(t.mapsettings.data.attr("center")),s=new google.maps.LatLng(i.lat,i.lng),o={center:s,zoom:parseInt(t.mapsettings.data.attr("zoom")),mapTypeId:t.mapsettings.data.attr("maptype")};this.map=new google.maps.Map(e[0],o),_.isUndefined(t.enable_infowindow)||(this.enable_infowindow=t.enable_infowindow),this.enable_infowindow&&(this.infowindow_ctrl=new n({map:this.map,enable_route:this.enable_route})),this.makeBrand(),this.bindAppState()},bindAppState:function(){this.enable_infowindow&&e.bind("clicked_feature",this.proxy(function(e,t,n){this.infowindow_ctrl.close();if(!t)return;var r=this.options.mapdata.getFeatureById(t);if(!r)return;r.enable_infowindow?this.infowindow_ctrl.open(r):r.bounds&&this.map.fitBounds(r.bounds)})),e.bind("zoomto_latlng",this.proxy(function(t,n,i){if(!n)return;e.attr("zoomto_latlng",null);var s=r.parsePos(n);this.map.setCenter(s);var o=this.map.getZoom();o<15?this.map.setZoom(15):o>=15&&o<20&&this.map.setZoom(o+1)}))},addOverlay:function(e){if(e.state.attr("overlay_created"))return;var n=e.type;if(_.isUndefined(t[n]))return;var r={feature:e,map:this.map};n=="Marker"&&this.clusterer&&(r.clusterer=this.clusterer);var i=new t[n](r)},makeBrand:function(){var e=$('<div id="mapartisanbrand"><a href="http://www.mapcreator.pl" target="_blank">map created with <span class="brand-map">map</span><span class="brand-artisan">Creator</span></a></div>');e[0].index=1,this.map.controls[google.maps.ControlPosition.RIGHT_BOTTOM].push(e[0])},mapResize:function(){google.maps.event.trigger(this.map,"resize")},"{mapdata} add":function(e,t,n){this.addOverlay(n)}});return{MapCtrl:i}}),define("mapapp",["appstate","mapdata","maputil","mapcontrols","gm_mapctrl"],function(e,t,n,r,i){var s=can.Construct.extend({mapdata:null,maptree:null,mapctrl:null,mapsettings:null,map_el:null,init:function(e){if(_.isUndefined(e)||!e)e={};this.options=e},startMaping:function(e){this.map_el=e,this.bindAppState(),this.mapdata=new t.MapData,can.when(this.loadMapSettings()).then(this.proxy(this.setMapSettings)).then(this.proxy(this.loadStartData)).then(this.proxy(this.makeMenu)).fail(this.proxy(this.failStart))},setMapSettings:function(e){this.mapsettings=new t.MapSettings({data:e}),this.mapctrl=new i.MapCtrl(this.map_el,{mapsettings:this.mapsettings,mapdata:this.mapdata});var n=new can.Deferred;return n.resolve(),n},loadStartData:function(){return this.mapdata.loadFeatures("root")},loadMapSettings:function(){return typeof bootstrap_data=="undefined"||!bootstrap_data.mapsettings?t.MapSettingsModel.findOne({}):new can.Model(bootstrap_data.mapsettings)},makeMenu:function(){e.attr("admin_mode")&&(this.maptree=new r.MapTree("#menu-tree",{mapdata:this.mapdata}));var t=new can.Deferred;return t.resolve(),t},failStart:function(e){console.log("fail"),console.log(e)},bindAppState:function(){}});return s}),require(["maputil"],function(e){Mustache.registerHelper("trans",e.translate),Mustache.registerHelper("transp",e.translatep),$.ajaxSetup({cache:!1})}),define("globals",function(){}),require(["mapapp","globals"],function(e){$(function(){var t=new e({enable_menu_toggle:!0});t.startMaping(document.getElementById("map-box"))})}),define("main",function(){});
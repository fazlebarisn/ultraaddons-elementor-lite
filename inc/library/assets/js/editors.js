! function($, ELM, UA) {
    var a = {
        Views: {},
        Models: {},
        Collections: {},
        Behaviors: {},
        Layout: null,
        Manager: null
    };
    a.Models.Template = Backbone.Model.extend({
        defaults: {
            template_id: 0,
            title: "",
            type: "",
            thumbnail: "",
            url: "",
            tags: [],
            isPro: !1
        }
    }), a.Collections.Template = Backbone.Collection.extend({
        model: a.Models.Template
    });
    
    console.log(a);
    
}(jQuery, window.elementor, window.UltraAddons || {});
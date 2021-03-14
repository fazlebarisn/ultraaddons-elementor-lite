! function(e, t, i) {
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
    }), a.Behaviors.InsertTemplate = Marionette.Behavior.extend({
        ui: {
            insertButton: ".haTemplateLibrary__insert-button"
        },
        events: {
            "click @ui.insertButton": "onInsertButtonClick"
        },
        onInsertButtonClick: function() {
            i.library.insertTemplate({
                model: this.view.model
            })
        }
    }), a.Views.EmptyTemplateCollection = Marionette.ItemView.extend({
        id: "elementor-template-library-templates-empty",
        template: "#tmpl-haTemplateLibrary__empty",
        ui: {
            title: ".elementor-template-library-blank-title",
            message: ".elementor-template-library-blank-message"
        },
        modesStrings: {
            empty: {
                title: i.translate("templatesEmptyTitle"),
                message: i.translate("templatesEmptyMessage")
            },
            noResults: {
                title: i.translate("templatesNoResultsTitle"),
                message: i.translate("templatesNoResultsMessage")
            }
        },
        getCurrentMode: function() {
            return i.library.getFilter("text") ? "noResults" : "empty"
        },
        onRender: function() {
            var e = this.modesStrings[this.getCurrentMode()];
            this.ui.title.html(e.title), this.ui.message.html(e.message)
        }
    }), a.Views.Loading = Marionette.ItemView.extend({
        template: "#tmpl-haTemplateLibrary__loading",
        id: "haTemplateLibrary__loading"
    }), a.Views.Logo = Marionette.ItemView.extend({
        template: "#tmpl-haTemplateLibrary__header-logo",
        className: "haTemplateLibrary__header-logo",
        templateHelpers: function() {
            return {
                title: this.getOption("title")
            }
        }
    }), a.Views.BackButton = Marionette.ItemView.extend({
        template: "#tmpl-haTemplateLibrary__header-back",
        id: "elementor-template-library-header-preview-back",
        className: "haTemplateLibrary__header-back",
        events: function() {
            return {
                click: "onClick"
            }
        },
        onClick: function() {
            i.library.showBlocksView()
        }
    }), a.Views.Menu = Marionette.ItemView.extend({
        template: "#tmpl-haTemplateLibrary__header-menu",
        id: "elementor-template-library-header-menu",
        className: "haTemplateLibrary__header-menu",
        templateHelpers: function() {
            return i.library.getTabs()
        }
    }), a.Views.ResponsiveMenu = Marionette.ItemView.extend({
        template: "#tmpl-haTemplateLibrary__header-menu-responsive",
        id: "elementor-template-library-header-menu-responsive",
        className: "haTemplateLibrary__header-menu-responsive",
        ui: {
            items: "> .elementor-component-tab"
        },
        events: {
            "click @ui.items": "onTabItemClick"
        },
        onTabItemClick: function(t) {
            var a = e(t.currentTarget),
                n = a.data("tab");
            i.library.channels.tabs.trigger("change:device", n, a)
        }
    }), a.Views.Actions = Marionette.ItemView.extend({
        template: "#tmpl-haTemplateLibrary__header-actions",
        id: "elementor-template-library-header-actions",
        ui: {
            sync: "#haTemplateLibrary__header-sync i"
        },
        events: {
            "click @ui.sync": "onSyncClick"
        },
        onSyncClick: function() {
            var e = this;
            e.ui.sync.addClass("eicon-animation-spin"), i.library.requestLibraryData({
                onUpdate: function() {
                    e.ui.sync.removeClass("eicon-animation-spin"), i.library.updateBlocksView()
                },
                forceUpdate: !0,
                forceSync: !0
            })
        }
    }), a.Views.InsertWrapper = Marionette.ItemView.extend({
        template: "#tmpl-haTemplateLibrary__header-insert",
        id: "elementor-template-library-header-preview",
        behaviors: {
            insertTemplate: {
                behaviorClass: a.Behaviors.InsertTemplate
            }
        }
    }), a.Views.Preview = Marionette.ItemView.extend({
        template: "#tmpl-haTemplateLibrary__preview",
        className: "haTemplateLibrary__preview",
        ui: function() {
            return {
                iframe: "> iframe"
            }
        },
        onRender: function() {
            this.ui.iframe.attr("src", this.getOption("url")).hide();
            var e = this,
                t = (new a.Views.Loading).render();
            this.$el.append(t.el), this.ui.iframe.on("load", function() {
                e.$el.find("#haTemplateLibrary__loading").remove(), e.ui.iframe.show()
            })
        }
    }), a.Views.TemplateCollection = Marionette.CompositeView.extend({
        template: "#tmpl-haTemplateLibrary__templates",
        id: "haTemplateLibrary__templates",
        childViewContainer: "#haTemplateLibrary__templates-list",
        emptyView: function() {
            return new a.Views.EmptyTemplateCollection
        },
        ui: {
            templatesWindow: ".haTemplateLibrary__templates-window",
            textFilter: "#haTemplateLibrary__search",
            tagsFilter: "#haTemplateLibrary__filter-tags",
            filterBar: "#haTemplateLibrary__toolbar-filter"
        },
        events: {
            "input @ui.textFilter": "onTextFilterInput",
            "click @ui.tagsFilter li": "onTagsFilterClick"
        },
        getChildView: function(e) {
            return a.Views.Template
        },
        initialize: function() {
            this.listenTo(i.library.channels.templates, "filter:change", this._renderChildren)
        },
        filter: function(e) {
            var t = i.library.getFilterTerms(),
                a = !0;
            return _.each(t, function(t, n) {
                var r = i.library.getFilter(n);
                if (r && t.callback) {
                    var o = t.callback.call(e, r);
                    return o || (a = !1), o
                }
            }), a
        },
        setMasonrySkin: function() {
            var e = new elementorModules.utils.Masonry({
                container: this.$childViewContainer,
                items: this.$childViewContainer.children()
            });
            this.$childViewContainer.imagesLoaded(e.run.bind(e))
        },
        onRenderCollection: function() {
            this.setMasonrySkin(), this.updatePerfectScrollbar()
        },
        onTextFilterInput: function() {
            var e = this;
            _.defer(function() {
                i.library.setFilter("text", e.ui.textFilter.val())
            })
        },
        onTagsFilterClick: function(t) {
            var a = e(t.currentTarget),
                n = a.data("tag");
            i.library.setFilter("tags", n), a.addClass("active").siblings().removeClass("active"), n = n ? i.library.getTags()[n] : "Filter", this.ui.filterBar.find(".haTemplateLibrary__filter-btn").html(n)
        },
        updatePerfectScrollbar: function() {
            this.perfectScrollbar || (this.perfectScrollbar = new PerfectScrollbar(this.ui.templatesWindow[0], {
                suppressScrollX: !0
            })), this.perfectScrollbar.isRtl = !1, this.perfectScrollbar.update()
        },
        setTagsFilterHover: function() {
            var e = this;
            e.ui.filterBar.hoverIntent(function() {
                e.ui.tagsFilter.css("display", "block"), e.ui.filterBar.find(".haTemplateLibrary__filter-btn i").addClass("eicon-caret-down").removeClass("eicon-caret-right")
            }, function() {
                e.ui.tagsFilter.css("display", "none"), e.ui.filterBar.find(".haTemplateLibrary__filter-btn i").addClass("eicon-caret-right").removeClass("eicon-caret-down")
            }, {
                sensitivity: 50,
                interval: 150,
                timeout: 100
            })
        },
        onRender: function() {
            this.setTagsFilterHover(), this.updatePerfectScrollbar()
        }
    }), a.Views.Template = Marionette.ItemView.extend({
        template: "#tmpl-haTemplateLibrary__template",
        className: "haTemplateLibrary__template",
        ui: {
            previewButton: ".haTemplateLibrary__preview-button, .haTemplateLibrary__template-preview"
        },
        events: {
            "click @ui.previewButton": "onPreviewButtonClick"
        },
        behaviors: {
            insertTemplate: {
                behaviorClass: a.Behaviors.InsertTemplate
            }
        },
        onPreviewButtonClick: function() {
            i.library.showPreviewView(this.model)
        }
    }), a.Modal = elementorModules.common.views.modal.Layout.extend({
        getModalOptions: function() {
            return {
                id: "haTemplateLibrary__modal",
                hide: {
                    onOutsideClick: !1,
                    onEscKeyPress: !0,
                    onBackgroundClick: !1
                }
            }
        },
        getTemplateActionButton: function(e) {
            var t = e.isPro && !HappyAddonsEditor.hasPro ? "pro-button" : "insert-button";
            return viewId = "#tmpl-haTemplateLibrary__" + t, template = Marionette.TemplateCache.get(viewId), Marionette.Renderer.render(template)
        },
        showLogo: function(e) {
            this.getHeaderView().logoArea.show(new a.Views.Logo(e))
        },
        showDefaultHeader: function() {
            this.showLogo({
                title: "HAPPY LIBRARY"
            });
            var e = this.getHeaderView();
            e.tools.show(new a.Views.Actions), e.menuArea.reset()
        },
        showPreviewView: function(e) {
            var t = this.getHeaderView();
            t.menuArea.show(new a.Views.ResponsiveMenu), t.logoArea.show(new a.Views.BackButton), t.tools.show(new a.Views.InsertWrapper({
                model: e
            })), this.modalContent.show(new a.Views.Preview({
                url: e.get("url")
            }))
        },
        showBlocksView: function(e) {
            this.modalContent.show(new a.Views.TemplateCollection({
                collection: e
            }))
        }
    }), a.Manager = function() {
        function i() {
            var i = e(this).closest(".elementor-top-section"),
                a = i.data("id"),
                n = t.documents.getCurrent().container.children,
                r = i.prev(".elementor-add-section");
            n && _.each(n, function(e, t) {
                a === e.id && (m.atIndex = t)
            }), r.find(".elementor-add-ha-button").length || r.find(FIND_SELECTOR).before($openLibraryButton)
        }

        function n(e) {
            var t = e.find(FIND_SELECTOR);
            t.length && !e.find(".elementor-add-ha-button").length && t.before($openLibraryButton), e.on("click.onAddElement", ".elementor-editor-section-settings .elementor-editor-element-add", i)
        }

        function r(t, i) {
            i.addClass("elementor-active").siblings().removeClass("elementor-active");
            var a = devicesResponsiveMap[t] || devicesResponsiveMap.desktop;
            e(".haTemplateLibrary__preview").css("width", a)
        }

        function o() {
            var e = window.elementor.$previewContents,
                t = setInterval(function() {
                    n(e), e.find(".elementor-add-new-section").length > 0 && clearInterval(t)
                }, 100);
            e.on("click.onAddTemplateButton", ".elementor-add-ha-button", m.showModal.bind(m)), this.channels.tabs.on("change:device", r)
        }
        var l, s, d, c, m = this;
        FIND_SELECTOR = ".elementor-add-new-section .elementor-add-section-drag-title", $openLibraryButton = '<div class="elementor-add-section-area-button elementor-add-ha-button"> <i class="uicon uicon-ultraaddons"></i> </div>', devicesResponsiveMap = {
            desktop: "100%",
            tab: "768px",
            mobile: "360px"
        }, this.atIndex = -1, this.channels = {
            tabs: Backbone.Radio.channel("tabs"),
            templates: Backbone.Radio.channel("templates")
        }, this.updateBlocksView = function() {
            m.setFilter("tags", "", !0), m.setFilter("text", "", !0), m.getModal().showBlocksView(d)
        }, this.setFilter = function(e, t, i) {
            m.channels.templates.reply("filter:" + e, t), i || m.channels.templates.trigger("filter:change")
        }, this.getFilter = function(e) {
            return m.channels.templates.request("filter:" + e)
        }, this.getFilterTerms = function() {
            return {
                tags: {
                    callback: function(e) {
                        return _.any(this.get("tags"), function(t) {
                            return t.indexOf(e) >= 0
                        })
                    }
                },
                text: {
                    callback: function(e) {
                        return e = e.toLowerCase(), this.get("title").toLowerCase().indexOf(e) >= 0 || _.any(this.get("tags"), function(t) {
                            return t.indexOf(e) >= 0
                        })
                    }
                }
            }
        }, this.showModal = function() {
            m.getModal().showModal(), m.showBlocksView()
        }, this.closeModal = function() {
            this.getModal().hideModal()
        }, this.getModal = function() {
            return l || (l = new a.Modal), l
        }, this.init = function() {
            t.on("preview:loaded", o.bind(this))
        }, this.getTabs = function() {
            return {
                tabs: {
                    blocks: {
                        title: "Blocks",
                        active: !0
                    }
                }
            }
        }, this.getTags = function() {
            return s
        }, this.showBlocksView = function() {
            m.getModal().showDefaultHeader(), m.setFilter("tags", "", !0), m.setFilter("text", "", !0), m.loadTemplates(function() {
                m.getModal().showBlocksView(d)
            })
        }, this.showPreviewView = function(e) {
            m.getModal().showPreviewView(e)
        }, this.loadTemplates = function(e) {
            m.requestLibraryData({
                onBeforeUpdate: m.getModal().showLoadingView.bind(m.getModal()),
                onUpdate: function() {
                    m.getModal().hideLoadingView(), e && e()
                }
            })
        }, this.requestLibraryData = function(e) {
            if (d && !e.forceUpdate) return void(e.onUpdate && e.onUpdate());
            e.onBeforeUpdate && e.onBeforeUpdate();
            var t = {
                data: {},
                success: function(t) {
                    d = new a.Collections.Template(t.templates), t.tags && (s = t.tags), e.onUpdate && e.onUpdate()
                }
            };
            e.forceSync && (t.data.sync = !0), elementorCommon.ajax.addRequest("get_ha_library_data", t)
        }, this.requestTemplateData = function(e, t) {
            var i = {
                unique_id: e,
                data: {
                    edit_mode: !0,
                    display: !0,
                    template_id: e
                }
            };
            t && jQuery.extend(!0, i, t), elementorCommon.ajax.addRequest("get_ha_template_data", i)
        }, this.insertTemplate = function(e) {
            var t = e.model,
                i = this;
            i.getModal().showLoadingView(), i.requestTemplateData(t.get("template_id"), {
                success: function(e) {
                    i.getModal().hideLoadingView(), i.getModal().hideModal();
                    var a = {}; - 1 !== i.atIndex && (a.at = i.atIndex), $e.run("document/elements/import", {
                        model: t,
                        data: e,
                        options: a
                    }), i.atIndex = -1
                },
                error: function(e) {
                    i.showErrorDialog(e)
                },
                complete: function(e) {
                    i.getModal().hideLoadingView(), window.elementor.$previewContents.find(".elementor-add-section .elementor-add-section-close").click()
                }
            })
        }, this.showErrorDialog = function(e) {
            if ("object" == typeof e) {
                var t = "";
                _.each(e, function(e) {
                    t += "<div>" + e.message + ".</div>"
                }), e = t
            } else e ? e += "." : e = "<i>&#60;The error message is empty&#62;</i>";
            m.getErrorDialog().setMessage('The following error(s) occurred while processing the request:<div id="elementor-template-library-error-info">' + e + "</div>").show()
        }, this.getErrorDialog = function() {
            return c || (c = elementorCommon.dialogsManager.createWidget("alert", {
                id: "elementor-template-library-error-dialog",
                headerMessage: "An error occurred"
            })), c
        }
    }, i.library = new a.Manager, i.library.init(), window.ha = i
}(jQuery, window.elementor, window.ha || {});
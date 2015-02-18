/*! Shariff for bolt - v1.0.0 - 17.02.2015, https://gitlab.daniel-kulbe.de/root/bolt-shariff
 * @author: Daniel Kulbe <info@daniel-kulbe.de>, Licensed under the MIT
 * @based-on: shariff - v1.7.3 - 14.02.2015, https://github.com/heiseonline/shariff, Copyright (c) 2015 Ines Pauer, Philipp Busse, Sebastian Hilbig, Erich Kramer, Deniz Sesli, Licensed under the MIT <http://www.opensource.org/licenses/mit-license.php> license
 */
(function ($) {

    // Shariff Class definition
    // ==========================
    function Shariff (element, options) {
        this.$element = $(element);
        this.options  = options;
        this.$links   = this.$element.find('.shariff-button');

        !this.options.url.length && this.setUrl();

        this.replacePattern(),
        this.refresh(),
        this.initButtons(),
        this.debug(this);
    }

    Shariff.VERSION  = '1.0.0';

    Shariff.DEFAULTS = {
        debug: false,
        url: null,
        referrer: null,
        backend: null,
        params: {},
    };

    Shariff.prototype.debug = function (info) {
        this.options.debug && typeof window.console !== 'undefined' && window.console.debug(info);
    }

    Shariff.prototype.getMeta = function(name) {
        return $('meta[name="' + name + '"],[property="' + name + '"]').attr('content');
    };

    Shariff.prototype.updateQuery = function (options, url) {
        var link = window.document.createElement('a');
        link.href = url;

        if (!options)
            return (link.search = '') && link.href; // reset

        if (typeof options == 'string'){
            var o = {};
            $.each(options.replace(/^\?/,'').split('&'), function () {
                var p = this.split('=');
                o[p[0]] = p[1].length ? decodeURIComponent(p[1].replace(/\+/g,' ')) : true;
            }),
            options = o;
        }

        function parseValue (value) {
            return value === true ? '' : '=' + encodeURIComponent(value).replace(/'/g,"%27").replace(/"/g,"%22")
        }

        $.each(options, function (key, value) {
            var regex = new RegExp("((?:"+key+")(=[a-zA-Z0-9%]+)?(&)?)", 'gi');

/*update*/  if (regex.test(url)) {
                link.search = link.search.replace(regex, function () {
                    return !value ? '' : key + parseValue(value) + (typeof arguments[3] === 'string' ? arguments[3] : '');
                }) || link.search + (indexOf('?') ? '&' : '?') + key + parseValue(value);
/*add*/     } else {
                link.search += (link.search.length ? '&' : '') + encodeURIComponent(key) + parseValue(value);
            }
        });

        return link.href;
    };

    Shariff.prototype.getShares = function() {
        return $.getJSON(this.options.backend + '?url=' + encodeURIComponent(this.options.url));
    };

    Shariff.prototype.updateCounts = function(data) {
        var that = this;
        this.debug(data);
        $.each(data, function (key, value) {
            var $link = $('>[rel=popup]', that.$links.filter('.'+key));
            $link.length &&
                $('.share-count', $link).text(value >= 1000 ? Math.round(value / 1000) + 'k' : value) &&
                $link.trigger($.Event('update.shariff.count'));
        });
    };

    Shariff.prototype.setUrl = function() {
        var url = window.document.location.href;
        var canonical = $("link[rel=canonical]").attr("href") || this.getMeta("og:url") || "";

        if (canonical.length > 0) {
            if (canonical.indexOf("http") < 0) {
                canonical = window.document.location.protocol + "//" + window.document.location.host + canonical;
            }
            url = canonical;
        }

        this.debug('setUrl ' + url);

        return (this.options.url = url);
    };

    Shariff.prototype.replacePattern = function () {
        var name  = this.getMeta('og:site_name');
        var title = this.getMeta('og:title');
        var pattern = {
            '{{URL}}'  : this.options.url,
            '{{NAME}}' : (name || window.document.location.hostname),
            '{{TITLE}}': (title || $(title).text()),
            '{{SHARE}}': (name && title ? name + ' - ' + title : $(title).text()),
            '{{TEXT}}' : (this.getMeta('og:description') || this.getMeta('description')),
            '{{IMAGE}}': this.getMeta('og:image')
        };

        for (var issue in pattern) {
            var solution = pattern[issue];

            for (var service in this.options.params) {
                var params = this.options.params[service];

                for (var param in params) {
                    var value = params[param];

                    this.options.params[service][param] =
                        typeof solution == 'string' && value.length >= issue.length ?
                            value.replace(issue, solution) :
                            solution;
                }
            }
        }

        return this.options.params;
    };

    Shariff.prototype.initButtons = function () {
        var that = this;
        this.$links.each(function () {
            var link = $('a',this)[0];
            var classes = $(this).attr('class').split(' ');

            $.each(classes, function () {
                this in that.options.params &&
                    (link.href = that.updateQuery(that.options.params[this], link.href));

                that.options.referrer &&
                    (link.href = that.updateQuery(that.options.referrer, link.href));
            }),

            link.attributes.rel && link.attributes.rel.value === 'popup' &&
                $(this).on('click.shariff.popuptrigger', link, $.proxy(that.popup, that, link));

        });
    };

    Shariff.prototype.popup = function (link, event) {
        event.preventDefault();

        var that = this;
        var $this = $(link);
        var win = window.open($this.attr('href'), $this.attr('title'), 'width=600,height=460');

        this.refresh(event);
    };

    Shariff.prototype.refresh = function (event) {
        if (event) this.debug(event);
        this.options.backend !== null && this.getShares().then($.proxy(this.updateCounts, this), $.proxy(this.debug, this));
    };


    // Shariff Plugin definition
    // ===========================
    function Plugin(option) {
        return this.each(function () {
            var $this   = $(this);
            var data    = $this.data('shariff');
            var options = $.extend({}, Shariff.DEFAULTS, $this.data(), typeof option == 'object' && option);

            if (!data) $this.data('shariff', (data = new Shariff(this, options)));
        })
    }

    $.fn.shariff             = Plugin;
    $.fn.shariff.Constructor = Shariff;

    // Init Shariff
    // ==============
    $(window).on('load', function () {
        $('.shariff').each(function () {
            var $shariff = $(this);
            Plugin.call($shariff, $shariff.data());
        });
    });

})(jQuery);
(()=>{"use strict";const t=wp.blocks;function o(t){return o="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(t){return typeof t}:function(t){return t&&"function"==typeof Symbol&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t},o(t)}var n={"ramphor/testimonials":{save:function(){},edit:function(){}}},e=window.ramphor_testimonials||[];Object.keys(e).forEach((function(i){var r=e[i];console.log(r,i);var c=n[i]||null;"object"===o(c)&&(r.save=c.save||function(){},r.edit=c.edit||function(){},"function"==typeof c.customizeAttributes&&(r=c.customizeAttributes(r))),(0,t.registerBlockType)(i,r)}))})();
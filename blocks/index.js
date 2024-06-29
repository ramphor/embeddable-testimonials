import { registerBlockType } from "@wordpress/blocks";

import testimonials from "./testimonials";

const blockMethods = {
  'ramphor/testimonials': testimonials,
};

const blocks = window["ramphor_testimonials"] || [];

Object.keys(blocks).forEach((blockType) => {
  let blockAttributes = blocks[blockType];

  console.log(blockAttributes, blockType);

  const blockMethod = blockMethods[blockType] || null;
  if (typeof blockMethod === 'object') {
    blockAttributes.save = blockMethod.save || function(){};
    blockAttributes.edit = blockMethod.edit || function(){};

    if (typeof blockMethod.customizeAttributes === 'function') {
      blockAttributes = blockMethod.customizeAttributes(blockAttributes);
    }
  }
  registerBlockType(blockType, blockAttributes);
});

const Basket = async (nodeElement, createElementDecorator, page) => {
  const containsAsset = async assetCode => {
    const selectedAssetSelector = `[data-code="${assetCode}"]`;
    await nodeElement.$(selectedAssetSelector);
  };

  return {containsAsset};
};

module.exports = Basket;

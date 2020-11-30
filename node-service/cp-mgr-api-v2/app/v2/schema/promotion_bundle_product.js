/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('promotion_bundle_product', {
    prmt_bundle_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    prmt_det_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    applied_ids: {
      type: DataTypes.INTEGER(50),
      allowNull: true
    },
    product_qty: {
      type: DataTypes.INTEGER(50),
      allowNull: true
    }
  }, {
    tableName: 'promotion_bundle_product'
  });
};

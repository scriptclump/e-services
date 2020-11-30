/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('prmt_free_products', {
    prmt_free_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    prmt_det_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    applied_free_ids: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    quantity: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    }
  }, {
    tableName: 'prmt_free_products'
  });
};

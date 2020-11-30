/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('product_bin_config', {
    prod_bin_conf_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    wh_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    prod_group_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    bin_type_dim_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    pack_conf_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    min_qty: {
      type: DataTypes.INTEGER(8),
      allowNull: true
    },
    max_qty: {
      type: DataTypes.INTEGER(8),
      allowNull: true
    }
  }, {
    tableName: 'product_bin_config'
  });
};

/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('product_pack_color_warehouse', {
    color_wh_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    product_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    pack_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    le_wh_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    color_code: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    }
  }, {
    tableName: 'product_pack_color_warehouse'
  });
};

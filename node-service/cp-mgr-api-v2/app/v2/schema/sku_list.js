/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('sku_list', {
    sku_list_id: {
      type: DataTypes.INTEGER(15),
      allowNull: false,
      primaryKey: true
    },
    product_id: {
      type: DataTypes.INTEGER(15),
      allowNull: true
    },
    le_wh_id: {
      type: DataTypes.INTEGER(15),
      allowNull: true
    },
    status: {
      type: DataTypes.ENUM('1','0'),
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: true
    },
    created_by: {
      type: DataTypes.INTEGER(15),
      allowNull: true
    },
    updated_at: {
      type: DataTypes.DATE,
      allowNull: true
    },
    updated_by: {
      type: DataTypes.INTEGER(15),
      allowNull: true
    }
  }, {
    tableName: 'sku_list'
  });
};

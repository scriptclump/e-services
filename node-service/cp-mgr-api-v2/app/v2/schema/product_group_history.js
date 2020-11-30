/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('product_group_history', {
    product_grp_history_id: {
      type: DataTypes.INTEGER(15),
      allowNull: false,
      primaryKey: true
    },
    product_id: {
      type: DataTypes.INTEGER(15),
      allowNull: true
    },
    brand_id: {
      type: DataTypes.INTEGER(15),
      allowNull: true
    },
    category_id: {
      type: DataTypes.INTEGER(15),
      allowNull: true
    },
    manufacturer_id: {
      type: DataTypes.INTEGER(15),
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
    },
    value: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    product_grp_ref_id: {
      type: DataTypes.INTEGER(50),
      allowNull: true
    }
  }, {
    tableName: 'product_group_history'
  });
};

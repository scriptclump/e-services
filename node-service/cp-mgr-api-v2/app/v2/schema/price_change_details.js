/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('price_change_details', {
    pr_ch_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    product_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    stock: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    customer_type: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    le_wh_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    old_price: {
      type: DataTypes.DECIMAL,
      allowNull: false
    },
    new_price: {
      type: DataTypes.DECIMAL,
      allowNull: false
    },
    price_difference: {
      type: DataTypes.DECIMAL,
      allowNull: false
    },
    effective_date: {
      type: DataTypes.DATE,
      allowNull: false
    },
    created_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    updated_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    updated_at: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'price_change_details'
  });
};

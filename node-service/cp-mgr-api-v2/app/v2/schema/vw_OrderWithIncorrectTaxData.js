/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_OrderWithIncorrectTaxData', {
    gds_orders_tax_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    gds_order_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    product_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    gds_order_prod_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    tax_class: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    tax: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    tax_value: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    created_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'vw_OrderWithIncorrectTaxData'
  });
};

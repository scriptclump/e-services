/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('stock_indent_gen_cons', {
    stk_inde_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    product_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    sku: {
      type: DataTypes.STRING(500),
      allowNull: false,
      defaultValue: ''
    },
    manf: {
      type: DataTypes.STRING(500),
      allowNull: false
    },
    p_name: {
      type: DataTypes.STRING(500),
      allowNull: false
    },
    mrp: {
      type: DataTypes.DECIMAL,
      allowNull: false
    },
    cfc_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    DC01AWL: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    DC01AWL_val: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    }
  }, {
    tableName: 'stock_indent_gen_cons'
  });
};

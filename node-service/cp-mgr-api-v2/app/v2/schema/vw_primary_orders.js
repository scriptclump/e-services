/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_primary_orders', {
    le_code: {
      type: DataTypes.STRING(50),
      allowNull: false
    },
    le_wh_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    dc_name: {
      type: DataTypes.STRING(5000),
      allowNull: false
    },
    order_date: {
      type: DataTypes.DATEONLY,
      allowNull: false
    },
    ord_cnt: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    ord_val: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0.00'
    },
    inv_cnt: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    inv_val: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0.00'
    },
    ret_cnt: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    ret_val: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0.00'
    },
    cancl_cnt: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    cancl_val: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0.00'
    },
    del_cnt: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    del_val: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0.00'
    },
    collec_val: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0.00'
    },
    State: {
      type: DataTypes.STRING(75),
      allowNull: true
    },
    City: {
      type: DataTypes.STRING(75),
      allowNull: true
    }
  }, {
    tableName: 'vw_primary_orders'
  });
};

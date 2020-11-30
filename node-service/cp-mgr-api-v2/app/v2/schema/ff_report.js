/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('ff_report', {
    ff_rp_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    hub_name: {
      type: DataTypes.STRING(200),
      allowNull: true
    },
    beat: {
      type: DataTypes.STRING(400),
      allowNull: true
    },
    user_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    name: {
      type: DataTypes.STRING(220),
      allowNull: true
    },
    first_order: {
      type: DataTypes.DATE,
      allowNull: true
    },
    order_cnt: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    first_call: {
      type: DataTypes.DATE,
      allowNull: true
    },
    calls_cnt: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    tbv: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0.00000'
    },
    uob: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    abv: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0.00000'
    },
    tlc: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    ulc: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    alc: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0.00000'
    },
    contrib: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0.00000'
    },
    margin: {
      type: DataTypes.DECIMAL,
      allowNull: true,
      defaultValue: '0.00000'
    },
    cancel_ord_cnt: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    cancel_ord_val: {
      type: DataTypes.FLOAT,
      allowNull: true,
      defaultValue: '0.0000'
    },
    return_ord_cnt: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    return_ord_val: {
      type: DataTypes.FLOAT,
      allowNull: true,
      defaultValue: '0.0000'
    },
    ecash: {
      type: DataTypes.INTEGER(16),
      allowNull: true
    },
    role: {
      type: DataTypes.STRING(500),
      allowNull: true
    },
    commission: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0.00'
    },
    order_date: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    updated_at: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    delivered_margin: {
      type: DataTypes.DECIMAL,
      allowNull: true,
      defaultValue: '0.00000'
    },
    delivered_tbv: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0.00000'
    }
  }, {
    tableName: 'ff_report'
  });
};

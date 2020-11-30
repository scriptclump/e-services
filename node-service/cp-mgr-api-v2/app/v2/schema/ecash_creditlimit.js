/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('ecash_creditlimit', {
    ecash_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    state_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    dc_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    customer_type: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    creditlimit: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    segment_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    ecash: {
      type: DataTypes.FLOAT,
      allowNull: true
    },
    minimum_order_value: {
      type: DataTypes.FLOAT,
      allowNull: true
    },
    self_order_mov: {
      type: DataTypes.FLOAT,
      allowNull: true
    },
    mov_ordercount: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    created_date: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    updated_date: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'ecash_creditlimit'
  });
};

/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('trade_disc_det', {
    trade_disc_id: {
      type: DataTypes.INTEGER(30),
      allowNull: false,
      primaryKey: true
    },
    trade_name: {
      type: DataTypes.STRING(200),
      allowNull: true
    },
    ref_id: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    object_type: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    object_ids: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    warehouse_ids: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    state_ids: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    cust_types: {
      type: DataTypes.STRING(400),
      allowNull: true
    },
    cust_le_id: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    pack_type: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    from_range: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    to_range: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    disc_value: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    is_percent: {
      type: DataTypes.INTEGER(1),
      allowNull: true
    },
    cap_limit: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    from_date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    to_date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    is_active: {
      type: DataTypes.INTEGER(1),
      allowNull: true
    },
    is_self: {
      type: DataTypes.INTEGER(1),
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    created_by: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    updated_at: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    updated_by: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    }
  }, {
    tableName: 'trade_disc_det'
  });
};

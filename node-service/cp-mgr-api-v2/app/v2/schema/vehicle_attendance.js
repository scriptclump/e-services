/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vehicle_attendance', {
    veh_att_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    attn_date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    vehicle_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    vehicle_reg_no: {
      type: DataTypes.STRING(500),
      allowNull: true
    },
    is_present: {
      type: DataTypes.INTEGER(1),
      allowNull: true,
      defaultValue: '1'
    },
    reporting_time: {
      type: DataTypes.TIME,
      allowNull: true,
      defaultValue: '00:00:00'
    },
    source: {
      type: DataTypes.INTEGER(11),
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
    },
    reason: {
      type: DataTypes.STRING(5000),
      allowNull: true
    },
    approved_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    approved_at: {
      type: DataTypes.DATE,
      allowNull: true
    },
    updated_at: {
      type: DataTypes.DATE,
      allowNull: true
    }
  }, {
    tableName: 'vehicle_attendance'
  });
};

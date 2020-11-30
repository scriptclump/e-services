/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('consignment_scheduling_slots', {
    scheduling_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true,
      references: {
        model: 'inbound_requests',
        key: 'inbound_request_id'
      }
    },
    scheduling_date: {
      type: DataTypes.DATEONLY,
      allowNull: false
    },
    scheduling_timeSlot: {
      type: DataTypes.STRING(50),
      allowNull: false
    },
    scheduling_status: {
      type: DataTypes.STRING(50),
      allowNull: false
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
    tableName: 'consignment_scheduling_slots'
  });
};

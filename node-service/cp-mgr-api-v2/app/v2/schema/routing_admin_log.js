/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('routing_admin_log', {
    id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    hub_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    route_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    vehicle_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    vehicle_number_generated: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    no_of_orders: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    no_of_crates: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    vehicle_code: {
      type: DataTypes.TEXT,
      allowNull: false
    },
    route_data: {
      type: DataTypes.TEXT,
      allowNull: false
    },
    delivery_executive: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    delivery_executive_name: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    estimated_distance: {
      type: DataTypes.FLOAT,
      allowNull: true,
      defaultValue: '0'
    },
    estimated_time: {
      type: DataTypes.FLOAT,
      allowNull: true,
      defaultValue: '0'
    },
    status: {
      type: DataTypes.INTEGER(6).UNSIGNED,
      allowNull: false,
      defaultValue: '0'
    },
    trip_started_at: {
      type: DataTypes.DATE,
      allowNull: true
    },
    trip_ended_at: {
      type: DataTypes.DATE,
      allowNull: true
    }
  }, {
    tableName: 'routing_admin_log'
  });
};

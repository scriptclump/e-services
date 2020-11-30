/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('routing_unassigned_log', {
    id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    unassign_route_admin_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    mapped_route_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    order_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      unique: true
    },
    added_at: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'routing_unassigned_log'
  });
};

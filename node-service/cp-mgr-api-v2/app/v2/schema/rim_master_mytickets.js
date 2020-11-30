/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('rim_master_mytickets', {
    agency_index: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    assigned_to: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    ticket_count: {
      type: DataTypes.STRING(255),
      allowNull: true
    }
  }, {
    tableName: 'rim_master_mytickets'
  });
};

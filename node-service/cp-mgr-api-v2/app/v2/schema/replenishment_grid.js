/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('replenishment_grid', {
    replenishment_grid_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    rack: {
      type: DataTypes.STRING(30),
      allowNull: false
    },
    type: {
      type: DataTypes.ENUM('Priority','Regular'),
      allowNull: false
    },
    assigned_user: {
      type: DataTypes.STRING(45),
      allowNull: true
    },
    assigned_time: {
      type: DataTypes.DATE,
      allowNull: true
    },
    status: {
      type: DataTypes.ENUM('Open','Assigned','Expire','Completed'),
      allowNull: false,
      defaultValue: 'Open'
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    updated_at: {
      type: DataTypes.DATE,
      allowNull: true
    }
  }, {
    tableName: 'replenishment_grid'
  });
};

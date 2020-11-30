/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('routeadmins', {
    id: {
      type: DataTypes.INTEGER(10).UNSIGNED,
      allowNull: false,
      primaryKey: true
    },
    hub_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    veh_key_d: {
      type: DataTypes.STRING(50),
      allowNull: true,
      defaultValue: '0'
    },
    status: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    createdAt: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    updatedAt: {
      type: DataTypes.DATE,
      allowNull: true
    }
  }, {
    tableName: 'routeadmins'
  });
};

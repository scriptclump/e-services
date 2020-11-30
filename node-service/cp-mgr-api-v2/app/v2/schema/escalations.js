/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('escalations', {
    esc_id: {
      type: DataTypes.INTEGER(10),
      allowNull: false
    },
    ticket_type_id: {
      type: DataTypes.INTEGER(3),
      allowNull: true
    },
    emergency: {
      type: DataTypes.FLOAT,
      allowNull: true
    },
    high: {
      type: DataTypes.FLOAT,
      allowNull: true
    },
    medium: {
      type: DataTypes.FLOAT,
      allowNull: true
    },
    low: {
      type: DataTypes.FLOAT,
      allowNull: true
    },
    exception: {
      type: DataTypes.FLOAT,
      allowNull: true
    },
    dept_id: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    esc_phone: {
      type: DataTypes.STRING(50),
      allowNull: true
    }
  }, {
    tableName: 'escalations'
  });
};

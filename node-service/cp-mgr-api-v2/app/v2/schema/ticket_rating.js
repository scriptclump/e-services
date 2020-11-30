/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('ticket_rating', {
    ticket_id: {
      type: DataTypes.INTEGER(255),
      allowNull: false,
      defaultValue: '0',
      primaryKey: true
    },
    rated_staff: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    rated_by: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    rated_date: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    rating: {
      type: DataTypes.INTEGER(10),
      allowNull: true,
      defaultValue: '3'
    },
    comments: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    closed_rating: {
      type: DataTypes.INTEGER(1),
      allowNull: true,
      defaultValue: '0'
    }
  }, {
    tableName: 'ticket_rating'
  });
};

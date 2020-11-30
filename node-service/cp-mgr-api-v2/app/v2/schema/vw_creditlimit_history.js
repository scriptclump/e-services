/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_creditlimit_history', {
    From_Date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    To_Date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    Requested_Amount: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    NAME: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    STATUS: {
      type: DataTypes.STRING(12),
      allowNull: false,
      defaultValue: ''
    },
    description: {
      type: DataTypes.STRING(250),
      allowNull: true
    },
    legal_entity_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    }
  }, {
    tableName: 'vw_creditlimit_history'
  });
};

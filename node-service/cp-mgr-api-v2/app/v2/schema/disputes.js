/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('disputes', {
    dispute_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    legal_entity_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    dispute_type: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    transaction_type: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    transaction_id: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    transaction_date: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    reported_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    reported_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    status: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    }
  }, {
    tableName: 'disputes'
  });
};

/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('remittance_mapping', {
    remmittance_map_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    ledger_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    collection_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    remittance_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      references: {
        model: 'collection_remittance_history',
        key: 'remittance_id'
      }
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'remittance_mapping'
  });
};

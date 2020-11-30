/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('remittance_mapping_ofd_backup', {
    remmittance_map_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
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
      allowNull: false
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'remittance_mapping_ofd_backup'
  });
};

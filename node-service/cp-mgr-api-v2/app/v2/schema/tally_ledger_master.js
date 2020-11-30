/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('tally_ledger_master', {
    tlm_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    tlm_name: {
      type: DataTypes.STRING(255),
      allowNull: false,
      unique: true
    },
    tlm_group: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    sync_date: {
      type: DataTypes.DATE,
      allowNull: true
    },
    sync_source: {
      type: DataTypes.STRING(50),
      allowNull: true
    }
  }, {
    tableName: 'tally_ledger_master'
  });
};

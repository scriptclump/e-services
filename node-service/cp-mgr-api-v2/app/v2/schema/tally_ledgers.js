/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('tally_ledgers', {
    le_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    LedgerName: {
      type: DataTypes.STRING(272),
      allowNull: true
    },
    Alias: {
      type: DataTypes.STRING(16),
      allowNull: true
    },
    LedgerType: {
      type: DataTypes.STRING(13),
      allowNull: false,
      defaultValue: ''
    },
    mobile_no: {
      type: DataTypes.STRING(15),
      allowNull: false
    },
    FullName: {
      type: DataTypes.STRING(51),
      allowNull: false,
      defaultValue: ''
    }
  }, {
    tableName: 'tally_ledgers'
  });
};

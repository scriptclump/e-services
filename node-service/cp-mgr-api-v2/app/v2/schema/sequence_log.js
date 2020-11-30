/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('sequence_log', {
    seq_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    sequence_code: {
      type: DataTypes.STRING(50),
      allowNull: false
    },
    proc_name: {
      type: DataTypes.STRING(500),
      allowNull: false
    },
    upd_seq: {
      type: DataTypes.STRING(500),
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'sequence_log'
  });
};

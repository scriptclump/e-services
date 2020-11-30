/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('reference_no_log', {
    id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    reference_no: {
      type: DataTypes.STRING(150),
      allowNull: false
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'reference_no_log'
  });
};

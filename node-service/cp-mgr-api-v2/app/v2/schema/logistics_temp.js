/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('logistics_temp', {
    dc_id: {
      type: DataTypes.STRING(500),
      allowNull: false
    },
    del_val: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0.00'
    }
  }, {
    tableName: 'logistics_temp'
  });
};

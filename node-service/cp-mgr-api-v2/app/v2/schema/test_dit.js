/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('test_dit', {
    product_id: {
      type: DataTypes.INTEGER(5),
      allowNull: true
    },
    esp: {
      type: DataTypes.DECIMAL,
      allowNull: true
    }
  }, {
    tableName: 'test_dit'
  });
};

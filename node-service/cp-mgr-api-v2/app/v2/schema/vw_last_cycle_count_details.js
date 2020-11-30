/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_last_cycle_count_details', {
    DC/FC ID: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    DC/FC: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    Owner_Name: {
      type: DataTypes.STRING(51),
      allowNull: false,
      defaultValue: ''
    },
    Owner_Mobile: {
      type: DataTypes.STRING(15),
      allowNull: false
    },
    Last_Cycle_Count_Date: {
      type: DataTypes.STRING(19),
      allowNull: false,
      defaultValue: ''
    },
    Diff_Days: {
      type: DataTypes.BIGINT,
      allowNull: false,
      defaultValue: '0'
    }
  }, {
    tableName: 'vw_last_cycle_count_details'
  });
};
